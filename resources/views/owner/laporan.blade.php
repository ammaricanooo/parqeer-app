<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Rekap Parkir') }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ================= FILTER SECTION ================= --}}
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8">
            <div class="flex items-center mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Filter Laporan</h3>
            </div>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Mode --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Mode Rekap</label>
                    <select id="mode" name="mode" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="single" {{ $mode === 'single' ? 'selected' : '' }}>Harian (Satu Tanggal)</option>
                        <option value="range" {{ $mode === 'range' ? 'selected' : '' }}>Rentang Waktu</option>
                    </select>
                </div>

                <div id="singleDate" class="md:col-span-3 space-y-2" style="display: none;">
                    <label class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pilih Tanggal</label>
                    <input type="date" name="date" value="{{ $date ?? '' }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 transition-all">
                </div>

                <div id="rangeDate" class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4 space-y-0" style="display: none;">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ $from ?? '' }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ $to ?? '' }}" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <div class="md:col-span-4 flex items-center gap-3 pt-4 border-t border-gray-50 dark:border-gray-700">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-200 dark:shadow-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Terapkan Filter
                    </button>
                    
                    <a href="{{ route('owner.laporan') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 transition-all text-center">
                        Reset
                    </a>

                    <a href="{{ route('owner.laporan.export') }}?mode={{ $mode }}&from={{ $from }}&to={{ $to }}" class="inline-flex items-center px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-100 dark:shadow-none ml-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </form>
        </div>

        {{-- ================= SUMMARY CARDS ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-2xl shadow-xl transition-transform hover:scale-[1.02]">
                <div class="relative z-10 text-white">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-70">Total Terpilih</p>
                    <p class="text-3xl font-black mt-2">Rp {{ number_format($dailyTotal, 0, ',', '.') }}</p>
                </div>
                <div class="absolute right-0 top-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 p-6 rounded-2xl shadow-xl transition-transform hover:scale-[1.02]">
                <div class="relative z-10 text-white">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-70">Minggu Ini</p>
                    <p class="text-3xl font-black mt-2">Rp {{ number_format($weeklyTotal, 0, ',', '.') }}</p>
                </div>
                <div class="absolute right-0 top-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-purple-600 to-fuchsia-700 p-6 rounded-2xl shadow-xl transition-transform hover:scale-[1.02]">
                <div class="relative z-10 text-white">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-70">Bulan Ini</p>
                    <p class="text-3xl font-black mt-2">Rp {{ number_format($monthlyTotal, 0, ',', '.') }}</p>
                </div>
                <div class="absolute right-0 top-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- ================= RECAP DETAILS ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-all">
                <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
                    <h3 class="font-bold text-gray-700 dark:text-gray-200 flex items-center">
                        <span class="w-2 h-5 bg-blue-600 rounded-full mr-3"></span>
                        Rekap Kendaraan Masuk
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse ($vehicleRecap as $vehicle)
                        <div class="group flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-transparent hover:border-blue-200 dark:hover:border-blue-900 transition-all cursor-default">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-white dark:bg-gray-800 rounded-xl shadow-sm flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <span class="text-xl">🚗</span>
                                </div>
                                <span class="font-bold capitalize text-gray-700 dark:text-gray-300">{{ $vehicle->vehicle_type ?? 'Lainnya' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $vehicle->count }}</span>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Unit</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <img src="https://illustrations.popsy.co/gray/box.svg" class="w-24 h-24 mx-auto mb-4 opacity-50">
                            <p class="text-gray-400 italic">Tidak ada data kendaraan</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-all">
                <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
                    <h3 class="font-bold text-gray-700 dark:text-gray-200 flex items-center">
                        <span class="w-2 h-5 bg-emerald-500 rounded-full mr-3"></span>
                        Summary Transaksi
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Total Masuk --}}
                    <div class="flex justify-between items-center p-4 bg-blue-50/50 dark:bg-blue-900/20 rounded-2xl">
                        <div class="text-blue-700 dark:text-blue-300 font-semibold">Total Kendaraan Masuk</div>
                        <div class="text-2xl font-black text-blue-600">{{ $vehicleRecap->sum('count') }}</div>
                    </div>
                    {{-- Total Keluar --}}
                    <div class="flex justify-between items-center p-4 bg-emerald-50/50 dark:bg-emerald-900/20 rounded-2xl">
                        <div class="text-emerald-700 dark:text-emerald-300 font-semibold">Total Transaksi Selesai</div>
                        <div class="text-2xl font-black text-emerald-600">{{ $dailyData->sum('count') ?? 0 }}</div>
                    </div>
                    {{-- Still Parked --}}
                    <div class="flex justify-between items-center p-4 bg-orange-50/50 dark:bg-orange-900/20 rounded-2xl">
                        <div class="text-orange-700 dark:text-orange-300 font-semibold">Kendaraan Aktif di Area</div>
                        <div class="text-2xl font-black text-orange-600">{{ $stillParked ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const modeSelect = document.getElementById('mode');
        const singleDate = document.getElementById('singleDate');
        const rangeDate  = document.getElementById('rangeDate');
        
        function toggleDateInput() {
            if (!modeSelect) return;
            if (modeSelect.value === 'single') { 
                singleDate.style.display = 'block'; 
                rangeDate.style.display  = 'none'; 
            } else { 
                singleDate.style.display = 'none'; 
                rangeDate.style.display  = 'grid'; 
            }
        }
        
        if (modeSelect) { 
            modeSelect.addEventListener('change', toggleDateInput); 
            toggleDateInput(); 
        }
    </script>
</x-app-layout>