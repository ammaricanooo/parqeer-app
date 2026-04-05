<x-app-layout>
    <x-slot name="title">Transaksi Parkir</x-slot>

    <section class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-8">
            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="w-full lg:max-w-md">
                        <form action="{{ route('attendant.transaction.index') }}" class="relative flex">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-l-lg focus:ring-primary focus:border-primary text-sm"
                                placeholder="Cari Plat Nomor / ID...">
                            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-r-lg font-semibold text-sm hover:bg-primary/90 transition-all">
                                Cari
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('attendant.transaction.scan') }}"
                            class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-slate-900 text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-slate-800 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                            Scan QR
                        </a>
                        <a href="{{ route('attendant.transaction.create') }}"
                            class="flex-1 lg:flex-none flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-primary/90 transition-all shadow-md shadow-primary-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Kendaraan Masuk
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-primary rounded-full"></span>
                        Menunggu Pembayaran
                    </h2>
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $pendingPaymentTransactions->count() }} Kendaraan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    @if ($pendingPaymentTransactions->count())
                        <table class="w-full text-sm text-left whitespace-nowrap">
                            <thead class="bg-gray-50/80 text-gray-500 uppercase text-[11px] font-bold tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Info Kendaraan</th>
                                    <th class="px-6 py-4">Waktu Masuk</th>
                                    <th class="px-6 py-4 text-center">Durasi</th>
                                    <th class="px-6 py-4 text-right">Est. Bayar</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($pendingPaymentTransactions as $transaction)
                                    @php $paymentInfo = $transaction->calculatePayment(); @endphp
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="bg-slate-800 text-white px-3 py-2 rounded-md font-mono font-bold border-b-4 border-slate-900 text-center min-w-[100px]">
                                                    {{ $transaction->plate_number }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ $transaction->area->name ?? '-' }}</div>
                                                    <div class="text-xs text-gray-500 italic">{{ $transaction->vehicle_color }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-600">
                                            {{ $transaction->entry_time->format('H:i') }} <span class="text-[10px] text-gray-400">WIB</span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono font-bold text-primary">
                                            {{ $paymentInfo['hours'] }}j {{ $paymentInfo['duration_minutes'] % 60 }}m
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-rose-600 font-extrabold text-base">
                                                Rp{{ number_format($paymentInfo['amount'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('attendant.transaction.entry-receipt', $transaction->id) }}" target="_blank"
                                                   class="p-2 text-gray-400 hover:text-primary hover:bg-primary/10 rounded-full transition-all" title="Print Struk">
                                                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                                </a>
                                                <a href="{{ route('attendant.transaction.scan-ticket', $transaction->id) }}"
                                                   class="bg-primary text-white px-4 py-2 rounded-lg font-bold text-xs shadow-sm hover:shadow-md transition-all uppercase tracking-tight">
                                                    Bayar & Keluar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-12 text-center">
                            <p class="text-gray-400 italic">Antrean kosong. Belum ada kendaraan masuk.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden opacity-95">
                <div class="px-6 py-4 border-b border-gray-50 bg-slate-50 flex items-center gap-2">
                    <span class="flex w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <h2 class="text-md font-bold text-slate-700 uppercase tracking-wide">History Keluar Hari Ini</h2>
                </div>
                
                <div class="overflow-x-auto text-xs">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-400 font-bold uppercase">
                            <tr>
                                <th class="px-6 py-3">Plat Nomor</th>
                                <th class="px-6 py-3">Waktu (In - Out)</th>
                                <th class="px-6 py-3 text-right">Total Biaya</th>
                                <th class="px-6 py-3 text-center">Struk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($completedTransactions as $transaction)
                                <tr class="text-gray-500 hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded">
                                            {{ $transaction->plate_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="font-medium text-gray-400">{{ $transaction->entry_time->format('H:i') }}</span> 
                                        <svg class="inline w-3 h-3 mx-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                        <span class="font-bold text-gray-700">{{ $transaction->exit_time->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-right font-bold text-emerald-600">
                                        Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="flex justify-center gap-2 font-bold uppercase text-[10px]">
                                            <a href="{{ route('attendant.transaction.entry-receipt', $transaction->id) }}" target="_blank" class="text-primary hover:bg-blue-50 px-2 py-1 rounded">Masuk</a>
                                            <a href="{{ route('attendant.transaction.receipt-pdf', $transaction->id) }}" target="_blank" class="text-emerald-500 hover:bg-emerald-50 px-2 py-1 rounded">Keluar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 tracking-widest uppercase">Belum Ada Data Selesai</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</x-app-layout>