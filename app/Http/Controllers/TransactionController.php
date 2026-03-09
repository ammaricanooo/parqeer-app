<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Area;
use App\Models\Rate;
use App\Models\LogActivity;
use App\Services\TicketService;
use App\Helpers\ViewHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Menampilkan form input kendaraan masuk (create transaction)
     */
    public function create(): View
    {
        $areas = Area::with('rates')->get();

        // Perbarui occupied berdasarkan transaksi yang sedang berjalan
        foreach ($areas as $area) {
            $area->updateOccupancy();
        }

        // Hanya tampilkan area yang masih punya slot dan memiliki tepat satu tarif (agar area menentukan tipe kendaraan)
        $availableAreas = $areas->filter(fn($a) => $a->hasAvailableSlot() && $a->rates->count() === 1);

        return view('attendant.transaction.create', ['areas' => $availableAreas]);
    }

    /**
     * Simpan transaksi baru (kendaraan masuk)
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Transaction::class);

        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'plate_number' => 'required|string|max:20',
            'vehicle_color' => 'required|string|max:30',
            'area_id' => 'required|exists:areas,id',
            'entry_time' => 'required|date',
        ]);

        // Get area and check rates
        $area = Area::with('rates')->findOrFail($validated['area_id']);
        if ($area->rates->count() === 0) {
            return redirect()->back()->withInput()->with('error', 'Area tidak memiliki tarif terkonfigurasi. Hubungi admin.');
        }

        if ($area->rates->count() > 1) {
            return redirect()->back()->withInput()->with('error', 'Area memiliki lebih dari satu tarif (motor & mobil). Pilih area yang khusus untuk satu tipe atau perbaiki pengaturan area.');
        }

        $rate = $area->rates->first();

        // Pastikan area masih punya slot tersedia
        if (!$area->hasAvailableSlot()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Area yang dipilih sudah penuh. Silakan pilih area lain.');
        }

        // Atomic transaction: Create vehicle, transaction, and log activity together
        try {
            $transaction = DB::transaction(function () use ($validated, $area, $rate) {
                // Pastikan kita punya key vehicle_id (bisa tidak dikirim) dan gunakan pengecekan aman
                $vehicleId = $validated['vehicle_id'] ?? null;

                // Jika vehicle_id tidak ada, cari berdasarkan plate_number atau buat yang baru
                if (!$vehicleId) {
                    $vehicle = Vehicle::where('plate_number', $validated['plate_number'])->first();
                    if (!$vehicle) {
                        $vehicle = Vehicle::create([
                            'plate_number' => $validated['plate_number'],
                            'color' => $validated['vehicle_color'],
                            'type' => $rate->vehicle_type,
                        ]);
                    }
                    $validated['vehicle_id'] = $vehicle->id;
                }

                // Tambahkan user_id dan rate_id
                $validated['user_id'] = Auth::id();
                $validated['rate_id'] = $rate->id;
                $validated['status'] = 'in';

                // Konversi entry_time string ke datetime
                $validated['entry_time'] = Carbon::parse($validated['entry_time']);

                // Buat transaksi baru
                $transaction = Transaction::create($validated);

                // Update occupancy area (satu kendaraan masuk)
                $area->updateOccupancy();

                // Catat ke log activity
                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $validated['vehicle_id'],
                    'user_id' => Auth::id(),
                    'activity' => 'entry',
                    'plate_number' => $validated['plate_number'],
                    'vehicle_color' => $validated['vehicle_color'],
                    'description' => 'Kendaraan masuk di area ' . $area->name . ' pada ' . $validated['entry_time']->format('Y-m-d H:i'),
                ]);

                return $transaction;
            });

            // Redirect ke halaman struk entry agar petugas dapat cetak/scan QR
            return redirect()->route('attendant.transaction.entry-receipt', $transaction->id)
                ->with('success', 'Kendaraan masuk dicatat. ID Transaksi: ' . $transaction->id);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal merekam kendaraan masuk. Silakan coba lagi.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Ambil transaksi yang masih aktif (exit_time null)
        $activeTransactions = Transaction::whereNull('exit_time')
            ->with(['vehicle', 'area', 'rate'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil transaksi yang sudah selesai (hari ini)
        $completedTransactions = Transaction::whereNotNull('exit_time')
            ->whereDate('exit_time', today())
            ->with(['vehicle', 'area', 'rate'])
            ->orderBy('exit_time', 'desc')
            ->get();

        return view('attendant.transaction.index', compact('activeTransactions', 'completedTransactions'));
    }

    /**
     * Download tiket entry dalam format PDF (DOMPDF)
     */
    public function entryReceipt(Transaction $transaction): Response
    {
        $this->authorize('view', $transaction);

        $ticketService = new TicketService();
        return $ticketService->generateEntryTicketPdf($transaction);
    }

    /**
     * Halaman scanner QR untuk petugas (buka kamera, decode QR dan arahkan ke transaksi)
     */
    public function scanQr(): View
    {
        $this->authorize('viewAny', Transaction::class); // petugas diperbolehkan
        return view('attendant.transaction.scan_qr');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showExit(Transaction $transaction): View
    {
        if ($transaction->exit_time) {
            abort(403, 'Transaksi ini sudah selesai.');
        }

        return view('attendant.transaction.exit', compact('transaction'));
    }

    /**
     * Proses kendaraan keluar dan hitung tarif per jam
     */
    public function processExit(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        if ($transaction->exit_time) {
            return redirect()->back()->with('error', 'Transaksi sudah selesai.');
        }

        $validated = $request->validate([
            'exit_time' => 'required|date'
        ]);

        try {
            DB::transaction(function () use ($request, $transaction, $validated) {
                // Update transaksi dengan exit time
                $exitTime = Carbon::parse($validated['exit_time']);
                $transaction->processExit($exitTime);

                // Update occupancy area (kendaraan keluar)
                $transaction->area->updateOccupancy();

                // Catat ke log activity (keluar)
                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => Auth::id(),
                    'activity' => 'exit',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => 'Kendaraan keluar. Durasi: ' . $transaction->duration_minutes . ' menit. Total: ' . number_format((float) $transaction->amount, 2),
                ]);
            });

            return redirect()->route('attendant.transaction.receipt', $transaction->id)
                ->with('success', 'Kendaraan keluar dicatat. Silahkan cetak struk.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencatat kendaraan keluar. Silakan coba lagi.');
        }
    }

    /**
     * API: Hitung jumlah saat ini (real-time) untuk transaksi yang sedang berjalan
     */
    public function currentAmount(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        if ($transaction->exit_time) {
            return response()->json(['success' => true, 'amount' => (float)$transaction->amount, 'hours' => ceil($transaction->duration_minutes / 60), 'minutes' => $transaction->duration_minutes]);
        }

        $now = Carbon::now();
        $minutes = $transaction->entry_time->diffInMinutes($now);
        $hours = (int) ceil($minutes / 60);
        $amount = $transaction->rate->amount * $hours;

        return response()->json(['success' => true, 'amount' => (float)$amount, 'hours' => $hours, 'minutes' => $minutes]);
    }

    /**
     * Tampilkan receipt dengan form pembayaran
     */
    public function receipt(Transaction $transaction): Response|View|RedirectResponse
    {
        $this->authorize('view', $transaction);

        if (!$transaction->exit_time) {
            return redirect()->route('attendant.transaction.index')
                ->with('error', 'Transaksi belum selesai.');
        }

        return view('attendant.transaction.show-receipt', compact('transaction'));
    }

    /**
     * Download tiket keluar dalam format PDF (DOMPDF)
     */
    public function downloadExitReceipt(Transaction $transaction): Response
    {
        $this->authorize('view', $transaction);

        if (!$transaction->exit_time) {
            abort(403, 'Transaksi belum selesai.');
        }

        $ticketService = new TicketService();
        return $ticketService->generateExitTicketPdf($transaction);
    }

    /**
     * Proses pembayaran transaksi
     */
    public function pay(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->status === 'paid') {
            return redirect()->back()->with('info', 'Transaksi sudah dibayar.');
        }

        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:' . $transaction->amount,
        ]);

        try {
            DB::transaction(function () use ($validated, $transaction) {
                // Store paid amount and calculate change
                $paidAmount = (float) $validated['paid_amount'];
                $change = $paidAmount - (float) $transaction->amount;

                // Update transaction with payment info
                $transaction->paid_amount = $paidAmount;
                $transaction->change = $change;
                $transaction->status = 'paid';
                $transaction->save();

                // Log payment activity
                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => Auth::id(),
                    'activity' => 'payment',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => 'Pembayaran: Rp ' . number_format((float) $transaction->amount, 2) . ' | Dibayar: Rp ' . number_format($paidAmount, 2) . ' | Kembalian: Rp ' . number_format($change, 2),
                ]);
            });

            return redirect()->route('attendant.transaction.index')
                ->with('success', 'Pembayaran berhasil. Struk telah dicetak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Download struk pembayaran dalam format PDF (DOMPDF)
     */
    public function downloadPaymentReceipt(Transaction $transaction): Response
    {
        $this->authorize('view', $transaction);

        if ($transaction->status !== 'paid') {
            abort(403, 'Transaksi belum dibayar.');
        }

        $ticketService = new TicketService();
        return $ticketService->generatePaymentReceiptPdf($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
    /**
     * Search kendaraan berdasarkan plate number (untuk autocomplete)
     */
    public function searchVehicle(Request $request)
    {
        $search = $request->get('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $vehicles = Vehicle::where('plate_number', 'like', '%' . $search . '%')
            ->limit(10)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'label' => $v->plate_number . ' (' . $v->color . ')',
                'plate_number' => $v->plate_number,
                'color' => $v->color,
            ]);

        return response()->json($vehicles);
    }

    /**
     * Get rate berdasarkan area dan vehicle type (API untuk frontend)
     */
    public function getRate(Request $request)
    {
        $areaId = $request->get('area_id');

        if (!$areaId) {
            return response()->json(['success' => false]);
        }

        $area = Area::with('rates')->find($areaId);
        if (!$area) {
            return response()->json(['success' => false]);
        }

        if ($area->rates->count() !== 1) {
            // ambiguous or not configured
            return response()->json(['success' => false, 'message' => 'Area harus memiliki tepat satu tarif untuk tipe kendaraan agar dapat dipilih.']);
        }

        $rate = $area->rates->first();

        return response()->json(['success' => true, 'rate' => $rate, 'vehicle_type' => $rate->vehicle_type]);
    }
}
