<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot>

    <div class="py-8 px-6 md:px-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-indigo-700 p-6 rounded-2xl shadow-lg transition-transform hover:scale-105">
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-purple-100 uppercase tracking-wider">Jumlah Pengguna</p>
                        <p class="text-3xl font-bold text-white mt-1">{{ number_format($userCount ?? 0) }}</p>
                    </div>
                    <div class="absolute right-[-10px] bottom-[-10px] text-white opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-cyan-700 p-6 rounded-2xl shadow-lg transition-transform hover:scale-105">
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-green-100 uppercase tracking-wider">Jumlah Area</p>
                        <p class="text-3xl font-bold text-white mt-1">{{ number_format($areaCount ?? 0) }}</p>
                    </div>
                    <div class="absolute right-[-10px] bottom-[-10px] text-white opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-amber-400 to-yellow-600 p-6 rounded-2xl shadow-lg transition-transform hover:scale-105">
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-amber-100 uppercase tracking-wider">Jumlah Kendaraan</p>
                        <p class="text-3xl font-bold text-white mt-1">{{ number_format($carCount ?? 0) }}</p>
                    </div>
                    <div class="absolute right-[-10px] bottom-[-10px] text-white opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Sekilas Tarif per Area</h3>
                    <p class="text-sm text-gray-500">Daftar biaya parkir/layanan berdasarkan tipe kendaraan.</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($areasWithRates as $area)
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-3">
                                    <div class="w-2 h-6 bg-purple-500 rounded-full mr-3"></div>
                                    <span class="font-bold text-gray-700 dark:text-gray-300 capitalize">{{ $area->name }}</span>
                                </div>
                                
                                <div class="space-y-2">
                                    @if($area->rates->isEmpty())
                                        <p class="text-xs italic text-gray-400">Belum ada data tarif</p>
                                    @else
                                        @foreach($area->rates as $rate)
                                            <div class="flex justify-between items-center text-sm p-2 bg-white dark:bg-gray-800 rounded shadow-sm">
                                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ ucfirst($rate->vehicle_type) }}</span>
                                                <span class="font-semibold text-purple-600 dark:text-purple-400">Rp {{ number_format($rate->amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10">
                                <p class="text-gray-500">Tidak ada area yang ditemukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>