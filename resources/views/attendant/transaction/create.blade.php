<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Input Kendaraan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('petugas.transaction.store') }}" method="POST">
                        @csrf

                        <!-- Plat Nomor -->
                        <div class="mb-4">
                            <label for="plate_number" class="block text-sm font-medium mb-1">
                                Plat Nomor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="plate_number" name="plate_number" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                placeholder="Contoh: B 1234 XYZ"
                                required
                                value="{{ old('plate_number') }}"
                                list="vehicle_suggestions">
                            <datalist id="vehicle_suggestions"></datalist>
                            @error('plate_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Ketik plat nomor untuk cari kendaraan yang sudah terdaftar</p>
                        </div>

                        <!-- Warna Kendaraan -->
                        <div class="mb-4">
                            <label for="vehicle_color" class="block text-sm font-medium mb-1">
                                Warna Kendaraan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="vehicle_color" name="vehicle_color" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                placeholder="Contoh: Merah, Putih, Biru"
                                required
                                value="{{ old('vehicle_color') }}">
                            @error('vehicle_color')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipe Kendaraan -->
                        <div class="mb-4">
                            <label for="vehicle_type" class="block text-sm font-medium mb-1">
                                Tipe Kendaraan <span class="text-red-500">*</span>
                            </label>
                            <select id="vehicle_type" name="vehicle_type" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Motor</option>
                                <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Mobil</option>
                                <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>Truk</option>
                                <option value="bus" {{ old('vehicle_type') == 'bus' ? 'selected' : '' }}>Bus</option>
                            </select>
                            @error('vehicle_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Tipe kendaraan menentukan tarif di area yang dipilih</p>
                        </div>

                        <!-- Area Parkir -->
                        <div class="mb-4">
                            <label for="area_id" class="block text-sm font-medium mb-1">
                                Area Parkir <span class="text-red-500">*</span>
                            </label>
                            <select id="area_id" name="area_id" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih Area --</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tarif Info -->
                        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg" id="rate_info" style="display: none;">
                            <p class="text-sm font-medium mb-2">Tarif untuk Area & Tipe Kendaraan:</p>
                            <p id="rate_display" class="text-lg font-bold">-</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Tarif dihitung per jam</p>
                        </div>

                        <!-- Waktu Masuk -->
                        <div class="mb-6">
                            <label for="entry_time" class="block text-sm font-medium mb-1">
                                Waktu Masuk <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="entry_time" name="entry_time" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                required
                                value="{{ old('entry_time', now()->format('Y-m-d\TH:i')) }}">
                            @error('entry_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol -->
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Kendaraan Masuk
                            </button>
                            <a href="{{ route('petugas.transaction.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-complete plat nomor
        const plateInput = document.getElementById('plate_number');
        const colorInput = document.getElementById('vehicle_color');
        const datalist = document.getElementById('vehicle_suggestions');
        const areaSelect = document.getElementById('area_id');
        const vehicleTypeSelect = document.getElementById('vehicle_type');
        const rateInfo = document.getElementById('rate_info');
        const rateDisplay = document.getElementById('rate_display');

        plateInput.addEventListener('input', async function() {
            const value = this.value;
            if (value.length < 2) {
                datalist.innerHTML = '';
                return;
            }

            try {
                const response = await fetch(`{{ route('petugas.transaction.search-vehicle') }}?q=${encodeURIComponent(value)}`);
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

        // Show rate info saat area dan vehicle type dipilih
        async function updateRateInfo() {
            const areaId = areaSelect.value;
            const vehicleType = vehicleTypeSelect.value;

            if (!areaId || !vehicleType) {
                rateInfo.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`/petugas/transaction/get-rate?area_id=${areaId}&vehicle_type=${vehicleType}`);
                const data = await response.json();
                
                if (data.success && data.rate) {
                    rateDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(data.rate.amount)} / jam`;
                    rateInfo.style.display = 'block';
                } else {
                    rateDisplay.textContent = 'Tarif tidak ditemukan';
                    rateInfo.style.display = 'block';
                }
            } catch (error) {
                console.error('Error fetching rate:', error);
            }
        }

        areaSelect.addEventListener('change', updateRateInfo);
        vehicleTypeSelect.addEventListener('change', updateRateInfo);
    </script>
</x-app-layout>
