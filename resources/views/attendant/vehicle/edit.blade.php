<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kendaraan - ' . $vehicle->plate_number) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('petugas.vehicle.update', $vehicle->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Plat Nomor -->
                        <div class="mb-4">
                            <label for="plate_number" class="block text-sm font-medium mb-1">
                                Plat Nomor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="plate_number" name="plate_number" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                required
                                value="{{ old('plate_number', $vehicle->plate_number) }}">
                            @error('plate_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warna -->
                        <div class="mb-4">
                            <label for="color" class="block text-sm font-medium mb-1">
                                Warna <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="color" name="color" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                required
                                value="{{ old('color', $vehicle->color) }}">
                            @error('color')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipe Kendaraan -->
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium mb-1">
                                Tipe Kendaraan <span class="text-red-500">*</span>
                            </label>
                            <select id="type" name="type" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="motorcycle" {{ old('type', $vehicle->type) == 'motorcycle' ? 'selected' : '' }}>Motor</option>
                                <option value="car" {{ old('type', $vehicle->type) == 'car' ? 'selected' : '' }}>Mobil</option>
                                <option value="truck" {{ old('type', $vehicle->type) == 'truck' ? 'selected' : '' }}>Truk</option>
                                <option value="bus" {{ old('type', $vehicle->type) == 'bus' ? 'selected' : '' }}>Bus</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol -->
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('petugas.vehicle.show', $vehicle->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
