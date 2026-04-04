<x-app-layout>
    <x-slot name="title">
        Transaksi Parkir
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between p-5 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                            <span
                                class="flex w-3 h-3 bg-red-500 rounded-full mr-2 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                            Menunggu Pembayaran
                        </h2>
                        <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">Status: IN
                            (Kendaraan Di Dalam)</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <form action="{{ route('attendant.transaction.index') }}" class="relative group">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari plat nomor..."
                                class="w-full md:w-64 pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </form>
                        <a href="{{ route('attendant.transaction.create') }}"
                            class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                            + Baru
                        </a>
                    </div>
                </div>

                @if ($pendingPaymentTransactions->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead
                                class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 uppercase text-[11px] font-bold tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Plat Nomor</th>
                                    <th class="px-6 py-4">Info Kendaraan</th>
                                    <th class="px-6 py-4">Waktu Masuk</th>
                                    <th class="px-6 py-4 text-center">Durasi</th>
                                    <th class="px-6 py-4 text-right">Est. Bayar</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($pendingPaymentTransactions as $transaction)
                                    @php $paymentInfo = $transaction->calculatePayment(); @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="px-6 py-4 font-medium text-gray-400 italic">#{{ $transaction->id }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1.5 bg-gray-800 text-white dark:bg-gray-100 dark:text-gray-900 font-mono font-black rounded border-2 border-gray-700 text-base shadow-sm">
                                                {{ $transaction->plate_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                            <div class="font-semibold">{{ $transaction->area->name ?? '-' }}</div>
                                            <div class="text-[11px] opacity-70">{{ $transaction->vehicle_color }}</div>
                                        </td>
                                        <td class="px-6 py-4 font-medium">{{ $transaction->entry_time->format('H:i') }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center font-mono font-bold text-primary dark:text-blue-400">
                                            {{ $paymentInfo['hours'] }}j {{ $paymentInfo['duration_minutes'] % 60 }}m
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-red-600 dark:text-red-400 font-black text-base">
                                                Rp{{ number_format($paymentInfo['amount'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('attendant.transaction.entry-receipt', $transaction->id) }}"
                                                    target="_blank"
                                                    class="p-2 text-gray-400 hover:text-primary transition-colors"
                                                    title="Print Struk Masuk">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('attendant.transaction.scan-ticket', $transaction->id) }}"
                                                    class="bg-primary text-white px-4 py-2 rounded-lg font-bold text-xs shadow-md transition-all uppercase">
                                                    Bayar & Keluar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-10 text-center text-gray-400 italic">Antrean kosong. Belum ada kendaraan masuk.</div>
                @endif
            </div>

            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 opacity-90 hover:opacity-100 transition-opacity">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h2 class="text-lg font-bold text-gray-600 dark:text-gray-300 flex items-center italic">
                        <span class="flex w-3 h-3 bg-emerald-500 rounded-full mr-2"></span>
                        History Keluar Hari Ini
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/30 text-gray-400 uppercase font-black">
                            <tr>
                                <th class="px-6 py-3 italic">ID</th>
                                <th class="px-6 py-3">Plat</th>
                                <th class="px-6 py-3">Masuk</th>
                                <th class="px-6 py-3">Keluar</th>
                                <th class="px-6 py-3">Durasi</th>
                                <th class="px-6 py-3 text-right">Total Biaya</th>
                                <th class="px-6 py-3 text-center">Struk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @forelse ($completedTransactions as $transaction)
                                @php $hours = ceil($transaction->duration_minutes / 60); @endphp
                                <tr
                                    class="text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/20">
                                    <td class="px-6 py-3">#{{ $transaction->id }}</td>
                                    <td
                                        class="px-6 py-3 font-bold text-gray-700 dark:text-gray-200 uppercase font-mono">
                                        {{ $transaction->plate_number }}
                                    </td>
                                    <td class="px-6 py-3">{{ $transaction->entry_time->format('H:i') }}</td>
                                    <td class="px-6 py-3 font-bold">{{ $transaction->exit_time->format('H:i') }}</td>
                                    <td class="px-6 py-3 italic">{{ $hours }} jam</td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-700 dark:text-gray-200">
                                        Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="flex justify-center gap-2 font-bold uppercase text-[10px]">
                                            <a href="{{ route('attendant.transaction.entry-receipt', $transaction->id) }}"
                                                target="_blank" class="text-primary hover:underline">Masuk</a>
                                            <span class="text-gray-300">|</span>
                                            <a href="{{ route('attendant.transaction.receipt-pdf', $transaction->id) }}"
                                                target="_blank" class="text-emerald-500 hover:underline">Keluar</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"
                                        class="px-6 py-8 text-center opacity-50 font-bold tracking-widest uppercase text-xs">
                                        Belum Ada Data Selesai
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
