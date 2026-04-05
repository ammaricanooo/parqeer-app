<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Dashboard Owner') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- TOMBOL EXPORT --}}
        <div class="flex items-center gap-3 mb-6 justify-end">
            <a href="{{ route('owner.export.excel', request()->all()) }}"
                class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-200 ">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor Excel
            </a>
            <a href="{{ route('owner.export.pdf', request()->all()) }}"
                class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-rose-200 ">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13" />
                </svg>
                Ekspor PDF
            </a>
            @if(config('services.google_sheets.spreadsheet_id'))
                <a href="https://docs.google.com/spreadsheets/d/{{ config('services.google_sheets.spreadsheet_id') }}/edit"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-blue-200 ">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Google Sheets
                </a>
            @endif
        </div>
        {{-- FILTER SECTION --}}
        <div
            class="bg-white  p-5 rounded-2xl shadow-sm border border-gray-100 /50 mb-8 transition-all hover:shadow-md">
            <form method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
                <div class="lg:col-span-3 space-y-2">
                    <label
                        class="text-[11px] font-black uppercase text-gray-400  tracking-[0.1em] ml-1">Mode
                        Tampilan</label>
                    <select id="mode" name="mode" onchange="toggleDateInputs()"
                        class="w-full px-4 py-2.5 rounded-xl border-gray-200    focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                        <option value="single" {{ $mode === 'single' ? 'selected' : '' }}>📅 Laporan Harian</option>
                        <option value="range" {{ $mode === 'range' ? 'selected' : '' }}>📊 Rentang Kustom</option>
                    </select>
                </div>

                <div id="singleDateInput" class="{{ $mode === 'range' ? 'hidden' : '' }} lg:col-span-6 space-y-2">
                    <label
                        class="text-[11px] font-black uppercase text-gray-400  tracking-[0.1em] ml-1">Pilih
                        Tanggal</label>
                    <input type="date" name="date" value="{{ $date }}"
                        class="w-full px-4 py-2.5 rounded-xl border-gray-200    focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                </div>

                <div id="rangeDateInput"
                    class="{{ $mode === 'single' ? 'hidden' : '' }} lg:col-span-6 grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label
                            class="text-[11px] font-black uppercase text-gray-400  tracking-[0.1em] ml-1">Mulai</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200   focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[11px] font-black uppercase text-gray-400  tracking-[0.1em] ml-1">Selesai</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200   focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </div>
                </div>

                <div class="lg:col-span-3 flex gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gray-900 text-white font-bold rounded-xl transition-all active:scale-95 shadow-lg shadow-gray-200 ">
                        Terapkan
                    </button>
                    <a href="{{ route('owner.dashboard') }}"
                        class="px-4 py-2.5 bg-gray-100  text-gray-600  rounded-xl hover:bg-gray-200 transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- 4 METRIC CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Card 1: Revenue --}}
            <div
                class="relative overflow-hidden group p-6 bg-indigo-600 rounded-3xl shadow-xl shadow-indigo-100  transition-all hover:-translate-y-1">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700">
                </div>
                <p class="text-xs font-bold text-indigo-100 uppercase tracking-widest mb-1">Pendapatan Filter</p>
                <h4 class="text-2xl font-black text-white">Rp {{ number_format($filteredTotalRevenue, 0, ',', '.') }}
                </h4>
                <div
                    class="mt-4 flex items-center text-[10px] text-indigo-200 font-bold bg-white/10 w-fit px-2 py-1 rounded-lg border border-white/10">
                    <span class="mr-1">●</span> PERIODE TERPILIH
                </div>
            </div>

            {{-- Card 2: Units --}}
            <div
                class="p-6 bg-white  rounded-3xl border border-gray-100  shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-blue-50  rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black text-emerald-500 bg-emerald-50  px-2 py-1 rounded-lg uppercase">Selesai</span>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Unit Keluar</p>
                <h4 class="text-3xl font-black text-gray-900  mt-1">{{ $filteredTotalCount }}</h4>
            </div>

            {{-- Card 3: Monthly --}}
            <div
                class="p-6 bg-white  rounded-3xl border border-gray-100  shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-2.5 bg-emerald-50 rounded-xl text-emerald-600 font-bold text-sm">
                        Rp
                    </div>
                    <span
                        class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ now()->format('M Y') }}</span>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Omzet Bulanan</p>
                <h4 class="text-2xl font-black text-gray-900  mt-1">Rp
                    {{ number_format($monthRevenue, 0, ',', '.') }}</h4>
            </div>

            {{-- Card 4: Live --}}
            <div
                class="p-6 bg-white  rounded-3xl border border-gray-100  shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-orange-50 rounded-xl">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex items-center">
                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse mr-1.5"></span>
                        <span class="text-[10px] font-black text-orange-500 uppercase tracking-wider">LIVE</span>
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Parkir Aktif</p>
                <h4 class="text-3xl font-black text-gray-900  mt-1">{{ $currentlyParked }} <span
                        class="text-sm font-medium text-gray-400">Unit</span></h4>
            </div>
        </div>

        {{-- MAIN CONTENT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div class="lg:col-span-8 space-y-8">
                {{-- CHART --}}
                <div
                    class="bg-white  p-8 rounded-3xl border border-gray-100  shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-black text-xl text-gray-800  flex items-center">
                            <span class="w-1.5 h-6 bg-indigo-600 rounded-full mr-3 shadow-lg shadow-indigo-200"></span>
                            Tren Pendapatan
                        </h3>
                    </div>
                    <div class="h-80 w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                {{-- VEHICLE RECAP --}}
                <div
                    class="bg-white  p-8 rounded-3xl border border-gray-100  shadow-sm">
                    <h3 class="font-black text-xl text-gray-800  mb-8 flex items-center">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full mr-3 shadow-lg shadow-emerald-200"></span>
                        Distribusi Kendaraan
                    </h3>
                    @if ($vehicleRecap->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                            @foreach ($vehicleRecap as $vehicle)
                                <div
                                    class="p-5 bg-gray-50  rounded-2xl border border-gray-100 /50 transition hover:border-indigo-300 text-center group">
                                    <p
                                        class="text-[10px] font-black text-gray-400  uppercase tracking-[0.15em] mb-3 group-hover:text-indigo-500 transition-colors">
                                        {{ $vehicle->type }}
                                    </p>
                                    <p class="text-3xl font-black text-gray-900  leading-none">
                                        {{ $vehicle->count }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <div
                                class="w-20 h-20 mb-4 rounded-full bg-gray-50  flex items-center justify-center border border-dashed border-gray-200 ">
                                <svg class="w-10 h-10 opacity-20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-2.586 2.586a1 1 0 01-1.414 0L12 13.414l-2.586 2.586a1 1 0 01-1.414 0L6 13.414" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium italic">Belum ada data transaksi yang direkam</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SIDEBAR AREA --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="sticky top-8">
                    <h3 class="font-black text-xl text-gray-800  px-2 mb-6 flex items-center">
                        <span class="w-1.5 h-6 bg-orange-500 rounded-full mr-3 shadow-lg shadow-orange-200"></span>
                        Okupansi Real-time
                    </h3>

                    @forelse($areaOccupancy as $area)
                        <div
                            class="mb-4 p-5 bg-white  rounded-3xl border border-gray-100  shadow-sm overflow-hidden relative group">
                            @if ($area['status'] === 'full')
                                <div class="absolute top-0 right-0 h-1 w-full bg-red-500"></div>
                            @endif

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span
                                        class="font-black text-gray-800  text-sm block tracking-tight">{{ $area['name'] }}</span>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $area['occupied'] }}
                                        / {{ $area['capacity'] }} UNIT</span>
                                </div>
                                <div
                                    class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider
                                {{ $area['status'] === 'full' ? 'bg-red-50 text-red-600' : ($area['status'] === 'warning' ? 'bg-yellow-50 text-yellow-600' : 'bg-emerald-50 text-emerald-600 ') }}">
                                    {{ $area['status'] }}
                                </div>
                            </div>

                            <div
                                class="relative h-2.5 w-full bg-gray-100  rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 ease-[cubic-bezier(0.34,1.56,0.64,1)]
                                {{ $area['percentage'] >= 90 ? 'bg-red-500' : ($area['percentage'] >= 75 ? 'bg-orange-500' : 'bg-indigo-500') }}"
                                    style="width: {{ $area['percentage'] }}%">
                                </div>
                            </div>

                            <div class="mt-3 flex justify-end">
                                <span
                                    class="text-[11px] font-black text-gray-700 ">{{ $area['percentage'] }}%</span>
                            </div>
                        </div>
                    @empty
                        <div
                            class="text-center p-8 bg-gray-50  rounded-3xl border border-dashed border-gray-200 ">
                            <p class="text-sm text-gray-400">Area tidak ditemukan</p>
                        </div>
                    @endforelse

                    {{-- GOOGLE SHEETS INFO CARD --}}
                    @if(config('services.google_sheets.spreadsheet_id'))
                        <div class="p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl border border-blue-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-blue-100 rounded-xl">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Google Sheets Backup</h4>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest">Real-time Sync Active</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 mb-3">
                                Data transaksi otomatis disimpan ke Google Sheets sebagai backup. Klik tombol di atas untuk melihat data real-time.
                            </p>
                            <div class="flex items-center gap-2 text-[10px] text-green-600 font-bold">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                SYNC AKTIF
                            </div>
                        </div>
                    @else
                        <div class="p-5 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-3xl border border-yellow-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-yellow-100 rounded-xl">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Google Sheets Belum Setup</h4>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest">Backup Tidak Aktif</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 mb-3">
                                Setup Google Sheets untuk backup real-time data transaksi. Lihat dokumentasi GOOGLE_SHEETS_SETUP.md
                            </p>
                            <div class="flex items-center gap-2 text-[10px] text-orange-600 font-bold">
                                <div class="w-1.5 h-1.5 bg-orange-500 rounded-full"></div>
                                SETUP DIBUTUHKAN
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleDateInputs() {
            const mode = document.getElementById('mode').value;
            const single = document.getElementById('singleDateInput');
            const range = document.getElementById('rangeDateInput');

            if (mode === 'range') {
                single.classList.add('hidden');
                range.classList.remove('hidden');
            } else {
                single.classList.remove('hidden');
                range.classList.add('hidden');
            }
        }

        const ctx = document.getElementById('revenueChart')?.getContext('2d');
        if (ctx) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData->pluck('date')),
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json($chartData->pluck('total')),
                        borderColor: '#4f46e5',
                        backgroundColor: gradient,
                        borderWidth: 4,
                        tension: 0.45,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#4f46e5',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            displayColors: false,
                            callbacks: {
                                label: (context) => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                callback: (v) => 'Rp ' + (v >= 1000000 ? (v / 1000000).toFixed(1) + 'jt' : (v /
                                    1000).toFixed(0) + 'rb')
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 11,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
