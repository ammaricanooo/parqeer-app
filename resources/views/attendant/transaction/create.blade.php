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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white relative shadow-md sm:rounded-lg p-4">

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
                            <label for="plate_number"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Plat Nomor <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                id="plate_number"
                                name="plate_number"
                                list="vehicle_suggestions"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: B 1234 XYZ"
                                value="{{ old('plate_number') }}"
                                required>
                            <datalist id="vehicle_suggestions"></datalist>
                            <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
                        </div>

                        <!-- Warna Kendaraan -->
                        <div>
                            <label for="vehicle_color"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Warna Kendaraan <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                id="vehicle_color"
                                name="vehicle_color"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: Merah, Putih"
                                value="{{ old('vehicle_color') }}"
                                required>
                            <x-input-error :messages="$errors->get('vehicle_color')" class="mt-2" />
                        </div>

                        <!-- Area Parkir -->
                        <div>
                            <label for="area_id"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Area Parkir <span class="text-red-500">*</span>
                            </label>

                            @if($areas->isEmpty())
                                <div class="p-3 bg-red-50 rounded text-sm text-red-700">
                                    Tidak ada area tersedia (semua penuh)
                                </div>
                                <select disabled
                                    class="bg-gray-100 border border-gray-300 text-sm rounded-lg block w-full p-2.5 mt-2">
                                    <option>-- Tidak ada area --</option>
                                </select>
                            @else
                                <select id="area_id"
                                    name="area_id"
                                    class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                    required>
                                    <option value="">-- Pilih Area --</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                            ({{ $area->occupied }}/{{ $area->capacity }})
                                            - {{ ucfirst($area->rates->first()->vehicle_type) }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif

                            <x-input-error :messages="$errors->get('area_id')" class="mt-2" />
                        </div>

                        <!-- Waktu Masuk -->
                        <div>
                            <label for="entry_time"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Waktu Masuk <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local"
                                id="entry_time"
                                name="entry_time"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                value="{{ old('entry_time', now()->format('Y-m-d\TH:i')) }}"
                                required>
                            <x-input-error :messages="$errors->get('entry_time')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Info Tarif -->
                    <div id="rate_info"
                        class="hidden mt-6 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium">Tarif:</p>
                        <p id="rate_display" class="text-lg font-bold">-</p>
                        <p class="text-xs text-gray-600">Tarif dihitung per jam</p>
                    </div>

                    <!-- Button -->
                    <div class="block w-full mt-6">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 font-medium rounded-lg text-sm px-4 py-2"
                            {{ $areas->isEmpty() ? 'disabled' : '' }}>
                            Simpan Kendaraan Masuk
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>

        <script>
        // Auto-complete plat nomor
        const plateInput = document.getElementById('plate_number');
        const colorInput = document.getElementById('vehicle_color');
        const datalist = document.getElementById('vehicle_suggestions');
        const areaSelect = document.getElementById('area_id');
        const rateInfo = document.getElementById('rate_info');
        const rateDisplay = document.getElementById('rate_display');

        plateInput.addEventListener('input', async function() {
            const value = this.value;
            if (value.length < 2) {
                datalist.innerHTML = '';
                return;
            }

            try {
                const response = await fetch(`{{ route('attendant.transaction.search-vehicle') }}?q=${encodeURIComponent(value)}`);
                const vehicles = await response.json();
                
                datalist.innerHTML = '';
                vehicles.forEach(vehicle => {
                    const option = document.createElement('option');
                    option.value = vehicle.plate_number;
                    option.textContent = `${vehicle.plate_number} (${vehicle.color})`;
                    option.dataset.color = vehicle.color;
                    datalist.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching vehicles:', error);
            }
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
                    rateDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(data.rate.amount)} / jam - Tipe: ${data.vehicle_type}`;
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
