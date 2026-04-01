<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight transition-all">
            {{ __('Dashboard Rekap Singkat') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-indigo-700 p-6 rounded-2xl shadow-xl shadow-blue-200 dark:shadow-none transition-transform hover:-translate-y-1">
                    <div class="relative z-10 text-white">
                        <p class="text-sm font-medium opacity-80 uppercase tracking-widest">Hari Ini</p>
                        <p class="text-2xl font-extrabold mt-2">Rp {{ number_format($todayRevenue ?? 0,0,',','.') }}</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-20 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-teal-400 to-green-600 p-6 rounded-2xl shadow-xl shadow-teal-200 dark:shadow-none transition-transform hover:-translate-y-1">
                    <div class="relative z-10 text-white">
                        <p class="text-sm font-medium opacity-80 uppercase tracking-widest">Bulan Ini</p>
                        <p class="text-2xl font-extrabold mt-2">Rp {{ number_format($monthRevenue ?? 0,0,',','.') }}</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-20 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-yellow-400 to-amber-600 p-6 rounded-2xl shadow-xl shadow-yellow-200 dark:shadow-none transition-transform hover:-translate-y-1">
                    <div class="relative z-10 text-white">
                        <p class="text-sm font-medium opacity-80 uppercase tracking-widest">Sedang Parkir</p>
                        <p class="text-2xl font-extrabold mt-2">{{ number_format($currentlyParked ?? 0,0,',','.') }} <span class="text-sm font-normal">Kendaraan</span></p>
                    </div>
                    <div class="absolute -right-2 -bottom-2 opacity-20 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-rose-700 p-6 rounded-2xl shadow-xl shadow-red-200 dark:shadow-none transition-transform hover:-translate-y-1">
                    <div class="relative z-10 text-white">
                        <p class="text-sm font-medium opacity-80 uppercase tracking-widest">Total Pendapatan</p>
                        <p class="text-2xl font-extrabold mt-2">Rp {{ number_format($totalRevenue ?? 0,0,',','.') }}</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-20 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200 uppercase text-sm tracking-wider">Pendapatan per Area</h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-600 text-xs font-bold rounded-full">Statistik</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-6 py-3 font-semibold">Area</th>
                                    <th class="px-6 py-3 font-semibold text-center">Transaksi</th>
                                    <th class="px-6 py-3 font-semibold text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($revenuePerArea as $area)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">{{ $area->area_name }}</td>
                                    <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $area->tx_count }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($area->total,0,',','.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Data belum tersedia</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700 dark:text-gray-200 uppercase text-sm tracking-wider">Top Transaksi Terakhir</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-600 text-xs font-bold rounded-full">Live</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-6 py-3 font-semibold">Plat/Area</th>
                                    <th class="px-6 py-3 font-semibold">Waktu Keluar</th>
                                    <th class="px-6 py-3 font-semibold text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($topTransactions as $tx)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-sm">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 dark:text-gray-200">{{ $tx->vehicle->plate_number ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $tx->area->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                        {{ $tx->exit_time ? \Carbon\Carbon::parse($tx->exit_time)->format('d M, H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-emerald-600 dark:text-emerald-400 text-base">
                                        Rp {{ number_format($tx->amount ?? 0,0,',','.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Belum ada transaksi</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>