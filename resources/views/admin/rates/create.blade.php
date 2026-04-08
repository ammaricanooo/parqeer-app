<x-app-layout>
    <x-slot name="title">
        Tambah Data Tarif Parqeer
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Tarif Parqeer') }}
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg space-y-3 md:space-y-0 md:space-x-4 p-4">

                <!-- Title -->
                <div class="flex items-center px-4 py-2">
                    <h5 class="text-gray-800 font-semibold">Tambah Data Tarif Parkir</h5>
                </div>

                <div class="mx-4 py-4 border-t"></div>

                <!-- Form -->
                <form method="POST" action="{{ route('admin.rates.store') }}">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 md:gap-6">

                        <!-- Area -->
                        <div>
                            <label for="area_id"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Area
                            </label>
                            <select name="area_id" id="area_id"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                required>
                                <option value="">-- Pilih Area --</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('area_id')" class="mt-2" />
                        </div>

                        <!-- Vehicle Type -->
                        <div>
                            <label for="vehicle_type"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Jenis Kendaraan
                            </label>
                            <select name="vehicle_type" id="vehicle_type"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                required>
                                <option value="">-- Pilih Jenis Kendaraan --</option>
                                @foreach($vehicleTypes as $type)
                                    <option value="{{ $type->key }}" {{ old('vehicle_type') === $type->key ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('admin.vehicle-types.index') }}"
                                class="text-blue-600 hover:text-blue-800 cursor-pointer mt-1 block">Tambahkan tipe kendaraan</a>
                            <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
                        </div>
                        <!-- Amount -->
                        <div>
                            <label for="amount"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Tarif
                            </label>
                            <input type="number" name="amount" id="amount"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Masukkan tarif"
                                value="{{ old('amount') }}" required>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                                                <!-- Pricing Type -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tipe Harga</label>
                            <div class="flex gap-3">
                                <label class="flex-1 p-3 border rounded-xl cursor-pointer bg-white">
                                    <input type="radio" name="pricing_type" value="per_hour"
                                        class="mr-2" {{ old('pricing_type', 'per_hour') === 'per_hour' ? 'checked' : '' }}>
                                    Per Jam
                                </label>
                                <label class="flex-1 p-3 border rounded-xl cursor-pointer bg-white">
                                    <input type="radio" name="pricing_type" value="fixed"
                                        class="mr-2" {{ old('pricing_type') === 'fixed' ? 'checked' : '' }}>
                                    Harga Tetap
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('pricing_type')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="flex items-center justify-end gap-4 w-full mt-4">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Tambah Data Tarif Parkir
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</x-app-layout>
