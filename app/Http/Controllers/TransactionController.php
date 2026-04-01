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

        // Pastikan plat nomor tidak sedang aktif (in/paid)
        $existingOpen = Transaction::where('plate_number', $validated['plate_number'])
            ->whereIn('status', ['in', 'paid'])
            ->first();

        if ($existingOpen) {
            return redirect()->back()->withInput()->with('error', 'Plat nomor ini sudah tercatat sedang parkir (status ' . strtoupper($existingOpen->status) . ').');
        }

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
            return redirect()->route('attendant.transaction.entry-receipt', $transaction->id);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal merekam kendaraan masuk. Silakan coba lagi.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Auto-reset pembayaran yang sudah expired (lebih dari 1 jam dan belum keluar)
        $expiredPaidTransactions = Transaction::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->get();

        foreach ($expiredPaidTransactions as $transaction) {
            if ($transaction->isPaymentExpired()) {
                $transaction->resetExpiredPayment();

                // Log activity untuk audit trail
                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => null, // System action
                    'activity' => 'payment_expired',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => 'Pembayaran expired (1 jam) - status reset ke IN untuk pembayaran ulang',
                ]);
            }
        }

        // Kendaraan menunggu pembayaran (status 'in' - belum bayar atau pembayaran expired)
        $pendingPaymentTransactions = Transaction::where('status', 'in')
            ->with(['vehicle', 'area', 'rate'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Kendaraan sudah bayar dengan pembayaran masih valid (status 'paid' - siap keluar)
        $paidTransactions = Transaction::where('status', 'paid')
            ->with(['vehicle', 'area', 'rate'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Kendaraan sudah keluar (status 'out') - hari ini
        $completedTransactions = Transaction::where('status', 'out')
            ->whereDate('exit_time', today())
            ->with(['vehicle', 'area', 'rate'])
            ->orderBy('exit_time', 'desc')
            ->get();

        return view('attendant.transaction.index', compact('pendingPaymentTransactions', 'paidTransactions', 'completedTransactions'));
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
     * WORKFLOW BARU:
     * Tampilkan form pembayaran saat kendaraan mau keluar
     * (menggantikan exit form - sekarang hitung amount saat pembayaran, bukan saat keluar)
     */
    public function showExit(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);

        // Hanya bisa bayar jika status 'in' (belum bayar)
        if ($transaction->status !== 'in') {
            abort(403, 'Transaksi sudah dalam status "' . $transaction->status . '". Tidak bisa diproses pembayaran.');
        }

        // Hitung amount real-time berdasarkan durasi sekarang
        $paymentInfo = $transaction->calculatePayment();

        return view('attendant.transaction.payment', [
            'transaction' => $transaction,
            'duration' => $paymentInfo,
        ]);
    }

    /**
     * Gate scan endpoint untuk ticket QR (entry ticket). 1 URL tetap.
     * - status 'in' -> redirect ke payment page
     * - status 'paid' -> proses exit dan out
     * - status 'out' -> sudah selesai
     */
    public function scanTicket(Transaction $transaction): View|RedirectResponse
    {
        // Akses publik: scan ticket fisik di gate (bukan hanya attendant)
        if ($transaction->status === 'in') {
            return redirect()->route('payment.public', $transaction->id);
        }

        if ($transaction->status === 'paid') {
            try {
                DB::transaction(function () use ($transaction) {
                    $transaction->processExit(now());
                    $transaction->area->updateOccupancy();

                    LogActivity::create([
                        'transaction_id' => $transaction->id,
                        'vehicle_id' => $transaction->vehicle_id,
                        'user_id' => Auth::check() ? Auth::id() : null,
                        'activity' => 'exit',
                        'plate_number' => $transaction->plate_number,
                        'vehicle_color' => $transaction->vehicle_color,
                        'description' => 'Scan gate keluar: payment sudah paid, status jadi out',
                    ]);
                });

                return view('attendant.transaction.scan_ticket', [
                    'transaction' => $transaction,
                    'message' => '✅ Pembayaran sudah terkonfirmasi. Kendaraan berhasil keluar (status OUT).',
                ]);
            } catch (\Exception $e) {
                return view('attendant.transaction.scan_ticket', [
                    'transaction' => $transaction,
                    'error' => 'Gagal proses keluar: ' . $e->getMessage(),
                ]);
            }
        }

        return view('attendant.transaction.scan_ticket', [
            'transaction' => $transaction,
            'message' => '⚠ Transaksi sudah selesai (OUT).',
        ]);
    }

    /**
     * WORKFLOW BARU:
     * Proses pembayaran: hitung durasi, terima pembayaran, set status 'paid'
     * Kemudian kendaraan bisa keluar (update status ke 'out')
     */
    public function processExit(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        if ($transaction->status !== 'in') {
            return redirect()->back()->with('error', 'Status transaksi: ' . $transaction->status . '. Hanya transaksi dengan status "in" yang bisa diproses pembayaran.');
        }

        $paymentInfo = $transaction->calculatePayment();
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:' . $paymentInfo['amount'],
        ]);

        try {
            DB::transaction(function () use ($transaction, $validated) {
                $transaction->processPayment((float) $validated['paid_amount']);
                $transaction->processExit(now());
                $transaction->area->updateOccupancy();

                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => Auth::id(),
                    'activity' => 'payment_exit',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => 'Pembayaran & keluar: Rp ' . number_format((float) $transaction->amount, 2),
                ]);
            });

            return redirect()->route('attendant.transaction.index')
                ->with('success', 'Pembayaran selesai dan kendaraan keluar.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * WORKFLOW BARU:
     * Tampilkan form exit untuk kendaraan yang sudah membayar
     * Petugas input waktu keluar dan confirm
     */
    public function showExitVehicle(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);

        if ($transaction->status !== 'paid') {
            abort(403, 'Kendaraan harus sudah membayar (status "paid") untuk bisa keluar. Status saat ini: ' . $transaction->status);
        }

        return view('attendant.transaction.exit-vehicle', compact('transaction'));
    }

    /**
     * WORKFLOW BARU:
     * Proses keluar: set exit_time dan status dari 'paid' -> 'out'
     * Dipanggil setelah pembayaran selesai (scan QR atau manual)
     */
    public function exitVehicle(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        if ($transaction->status !== 'paid') {
            return redirect()->back()->with('error', 'Kendaraan harus sudah membayar (status "paid") untuk bisa keluar. Status saat ini: ' . $transaction->status);
        }

        try {
            DB::transaction(function () use ($transaction) {
                // Proses exit: hanya set exit_time dan status 'paid' -> 'out'
                $exitTime = Carbon::now();
                $transaction->processExit($exitTime);

                // Update occupancy area (kendaraan keluar)
                $transaction->area->updateOccupancy();

                // Catat ke log activity (exit)
                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => Auth::id(),
                    'activity' => 'exit',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => 'Kendaraan keluar. Durasi: ' . $transaction->duration_minutes . ' menit. Bayar: Rp ' . number_format((float) $transaction->amount, 2),
                ]);
            });

            // Redirect berdasarkan payment method
            return redirect()->route('attendant.transaction.index')
                ->with('success', 'Kendaraan keluar dicatat. Transaksi selesai.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * API: Hitung jumlah saat ini (real-time) untuk transaksi yang sedang berjalan (status 'in')
     * Dipanggil untuk form pembayaran agar bisa lihat estimate biaya
     */
    public function currentAmount(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        // Jika sudah dibayar, berikan data yang sudah dihitung
        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => true,
                'status' => 'paid',
                'amount' => (float) $transaction->amount,
                'hours' => ceil($transaction->duration_minutes / 60),
                'minutes' => $transaction->duration_minutes
            ]);
        }

        // Jika keluar, berikan data exit
        if ($transaction->status === 'out') {
            return response()->json([
                'success' => true,
                'status' => 'out',
                'amount' => (float) $transaction->amount,
                'hours' => ceil($transaction->duration_minutes / 60),
                'minutes' => $transaction->duration_minutes
            ]);
        }

        // Hitung real-time untuk status 'in'
        if ($transaction->status === 'in') {
            $paymentInfo = $transaction->calculatePayment();
            return response()->json([
                'success' => true,
                'status' => 'in',
                'amount' => (float) $paymentInfo['amount'],
                'hours' => $paymentInfo['hours'],
                'minutes' => $paymentInfo['duration_minutes']
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Status transaksi tidak dikenali']);
    }

    /**
     * Tampilkan receipt dengan form pembayaran dan payment gateway
     */
    public function receipt(Transaction $transaction): Response|View|RedirectResponse
    {
        $this->authorize('view', $transaction);

        // Tidak menggunakan QRIS gateway lagi: tampilkan form payment internal.
        return view('attendant.transaction.show-receipt', compact('transaction'));
    }

    /**
     * Download tiket keluar dalam format PDF (DOMPDF)
     */
    public function downloadExitReceipt(Transaction $transaction): Response
    {
        $this->authorize('view', $transaction);

        if ($transaction->status !== 'paid' && $transaction->status !== 'out') {
            abort(403, 'Transaksi belum selesai.');
        }

        $ticketService = new TicketService();
        return $ticketService->generateExitTicketPdf($transaction);
    }

    /**
     * DEPRECATED: Fungsi pay diganti dengan processExit yang baru
     * Disimpan untuk backward compatibility API jika ada yang masih pakai
     */
    public function pay(Request $request, Transaction $transaction): RedirectResponse
    {
        // Arahkan ke metode pembayaran yang baru
        return $this->processExit($request, $transaction);
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
     * Halaman pembayaran publika - dapat diakses tanpa login via QR code di tiket masuk
     * User bisa scan QR dari tiket masuk dan langsung bayar via QRIS
     * Amount di-lock saat halaman dibuka pertama kali (saat generatePaymentUrl() dipanggil)
     */
    public function publicPayment(Transaction $transaction): View
    {
        // Hanya bisa bayar jika status 'in' (belum bayar)
        if ($transaction->status !== 'in') {
            abort(403, 'Transaksi sudah dalam status "' . $transaction->status . '". Hanya transaksi dengan status "in" yang bisa dibayar.');
        }

        $transaction->refresh();

        $durationMinutes = $transaction->entry_time->diffInMinutes(now());
        $hours = intdiv($durationMinutes, 60);
        $minutes = $durationMinutes % 60;

        return view('public.payment', compact('transaction', 'hours', 'minutes'));
    }

    public function publicProcessPayment(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->status !== 'in') {
            return redirect()->route('payment.public', $transaction->id)
                ->with('error', 'Transaksi sudah tidak bisa dibayar (status: ' . $transaction->status . ').');
        }

        $paymentInfo = $transaction->calculatePayment();
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:' . $paymentInfo['amount'],
        ]);

        try {
            DB::transaction(function () use ($transaction, $validated) {
                $transaction->processPayment((float) $validated['paid_amount']);
                $transaction->processExit(now());
                $transaction->area->updateOccupancy();

                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'activity' => 'payment_exit',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => 'Pembayaran publik selesai, langsung keluar. Total Rp ' . number_format((float)$transaction->amount, 2),
                ]);
            });

            return redirect()->route('transaction.scan-ticket', $transaction->id)
                ->with('success', 'Pembayaran berhasil dan kendaraan sudah bisa keluar.');
        } catch (\Exception $e) {
            return redirect()->route('payment.public', $transaction->id)
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
