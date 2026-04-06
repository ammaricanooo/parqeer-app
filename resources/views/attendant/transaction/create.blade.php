<x-app-layout>
    <x-slot name="title">
        Kendaraan Masuk
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Kendaraan Masuk') }}
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg space-y-3 md:space-y-0 md:space-x-4 p-4">

                <!-- Title -->
                <div class="flex items-center px-4 py-2">
                    <h5 class="text-gray-800 font-semibold">Input Kendaraan Masuk</h5>
                </div>

                <div class="mx-4 py-4 border-t"></div>

                <!-- Form -->
                <form action="{{ route('attendant.transaction.store') }}" method="POST">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2 md:gap-6">

                        <!-- Plat Nomor -->
                        <div>
                            <label for="plate_number" class="block mb-2 text-sm font-medium text-gray-900">
                                Plat Nomor <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="plate_number" name="plate_number" list="vehicle_suggestions"
                                    class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                    placeholder="Contoh: B 1234 XYZ" value="{{ old('plate_number') }}" required>
                                <span id="plate_loading"
                                    class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-xs text-blue-600 font-semibold">Loading...</span>
                            </div>
                            <datalist id="vehicle_suggestions"></datalist>
                            <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
                        </div>

                        <!-- Warna Kendaraan -->
                        <div>
                            <label for="vehicle_color" class="block mb-2 text-sm font-medium text-gray-900">
                                Warna Kendaraan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="vehicle_color" name="vehicle_color"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: Merah, Putih" value="{{ old('vehicle_color') }}" required>
                            <x-input-error :messages="$errors->get('vehicle_color')" class="mt-2" />
                        </div>

                        <!-- Area Parkir -->
                        <div>
                            <label for="area_id" class="block mb-2 text-sm font-medium text-gray-900">
                                Area Parkir <span class="text-red-500">*</span>
                            </label>

                            @if ($areas->isEmpty())
                                <div class="p-3 bg-red-50 rounded text-sm text-red-700">
                                    Tidak ada area tersedia
                                </div>
                                <select disabled
                                    class="bg-gray-100 border border-gray-300 text-sm rounded-lg block w-full p-2.5 mt-2">
                                    <option>-- Tidak ada area --</option>
                                </select>
                            @else
                                <select id="area_id" name="area_id"
                                    class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                    required>
                                    <option value="">-- Pilih Area --</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                            ({{ $area->occupied }}/{{ $area->capacity }})
                                            - {{ ucfirst($area->rates->first()->vehicle_type) }} | {{ $area->rates->first()->amount }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif

                            <x-input-error :messages="$errors->get('area_id')" class="mt-2" />
                        </div>

                        <!-- Waktu Masuk -->
                        <div>
                            <label for="entry_time" class="block mb-2 text-sm font-medium text-gray-900">
                                Waktu Masuk <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="entry_time" name="entry_time"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                value="{{ old('entry_time', now()->format('Y-m-d\TH:i')) }}" required>
                            <x-input-error :messages="$errors->get('entry_time')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Info Tarif -->
                    <div id="rate_info" class="hidden mt-6 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium">Tarif:</p>
                        <p id="rate_display" class="text-lg font-bold">-</p>
                        <p class="text-xs text-gray-600">Tarif dihitung per jam</p>
                    </div>

                    <!-- Button -->
                    <div class="flex items-center justify-end gap-4 w-full mt-4">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none"
                            {{ $areas->isEmpty() ? 'disabled' : '' }}>
                            <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Simpan Kendaraan Masuk
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>

    <script>
        let debounceTimer;

        // Auto-complete plat nomor dari local database + API cek pajak
        const plateInput = document.getElementById('plate_number');
        const colorInput = document.getElementById('vehicle_color');
        const datalist = document.getElementById('vehicle_suggestions');
        const areaSelect = document.getElementById('area_id');
        const rateInfo = document.getElementById('rate_info');
        const rateDisplay = document.getElementById('rate_display');

        plateInput.addEventListener('input', async function() {
            // Clear previous timer
            clearTimeout(debounceTimer);

            const value = this.value.trim();
            if (value.length < 2) {
                datalist.innerHTML = '';
                return;
            }

            // Debounce 500ms agar tidak spam request
            debounceTimer = setTimeout(async () => {
                const loadingIndicator = document.getElementById('plate_loading');
                loadingIndicator.classList.remove('hidden');

                try {
                    // 1. Search dari local database terlebih dahulu
                    const localResponse = await fetch(
                        `/attendant/transaction/search/vehicle?q=${encodeURIComponent(value)}`
                    );
                    const localVehicles = await localResponse.json();

                    datalist.innerHTML = '';
                    localVehicles.forEach(vehicle => {
                        const option = document.createElement('option');
                        option.value = vehicle.plate_number;
                        option.textContent = `${vehicle.plate_number} (${vehicle.color})`;
                        option.dataset.color = vehicle.color;
                        datalist.appendChild(option);
                    });

                    // 2. Hubungi API cek pajak untuk mendapat informasi verifikasi
                    const formattedPlate = value.toUpperCase().trim();
                    const apiUrl =
                        `https://api.ammaricano.my.id/api/tools/cek-pajak/jabar?plat=${encodeURIComponent(formattedPlate)}`;

                    console.log('Calling API:', apiUrl);

                    const apiResponse = await fetch(apiUrl);
                    const apiData = await apiResponse.json();

                    if (apiData.success && apiData.result && apiData.result['informasi-umum']) {
                        const info = apiData.result['informasi-umum'];

                        // Auto-fill warna jika kosong
                        if (!colorInput.value || colorInput.value === old('vehicle_color')) {
                            colorInput.value = info['warna'] || '';
                        }

                        // Tambah info ke tooltip atau hidden field untuk referensi
                        console.log('Vehicle verified from API:', {
                            merk: info['merk'],
                            model: info['model'],
                            warna: info['warna'],
                            jenis: info['jenis'],
                            tahun: info['tahun-buatan']
                        });

                        // Suggest area berdasarkan jenis kendaraan dari API
                        const vehicleType = info['jenis']?.toLowerCase() || '';
                        if (vehicleType.includes('roda 2') || vehicleType.includes('motor')) {
                            // Suggest motor parking area (optional - atau autoselect jika hanya 1)
                        } else if (vehicleType.includes('roda 4') || vehicleType.includes(
                            'mobil') || vehicleType.includes('minibus')) {
                            // Suggest car parking area
                        }
                    }
                } catch (error) {
                    console.warn('API check failed (this is okay):', error.message);
                    // Jika API error, tidak apa-apa - user bisa input manual
                } finally {
                    document.getElementById('plate_loading').classList.add('hidden');
                }
            }, 500);
        });

        // Update warna otomatis saat pilih dari datalist
        plateInput.addEventListener('change', function() {
            const selectedOption = Array.from(datalist.options).find(opt => opt.value === this.value);
            if (selectedOption && selectedOption.dataset.color) {
                colorInput.value = selectedOption.dataset.color;
            }
        });

        // Show rate info saat area dipilih (area menentukan tipe kendaraan)
        async function updateRateInfo() {
            const areaId = areaSelect.value;

            if (!areaId) {
                rateInfo.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`/attendant/transaction/get-rate?area_id=${areaId}`);
                const data = await response.json();

                if (data.success && data.rate) {
                    rateDisplay.textContent =
                        `Rp ${new Intl.NumberFormat('id-ID').format(data.rate.amount)} / jam - Tipe: ${data.vehicle_type}`;
                    rateInfo.style.display = 'block';
                } else {
                    rateDisplay.textContent = data.message || 'Tarif tidak ditemukan';
                    rateInfo.style.display = 'block';
                }
            } catch (error) {
                console.error('Error fetching rate:', error);
            }
        }

        areaSelect.addEventListener('change', updateRateInfo);
    </script>
</x-app-layout>
