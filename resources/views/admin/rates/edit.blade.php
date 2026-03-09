<x-app-layout>
    <x-slot name="title">
        Edit Data Tarif Parkir
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Tarif Parkir') }}
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white relative shadow-md sm:rounded-lg p-4">

                <!-- Title -->
                <div class="flex items-center px-4 py-2">
                    <h5 class="text-gray-800 font-semibold">Edit Data Tarif Parkir</h5>
                </div>

                <div class="mx-4 py-4 border-t"></div>

                <!-- Form -->
                <form method="POST" action="{{ route('admin.rates.update', $rate->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-4 md:gap-6">

                        <!-- Area -->
                        <div>
                            <label for="area_id"
                                class="block mb-2 text-sm font-medium text-gray-900">
                                Area
                            </label>
                            <select name="area_id" id="area_id"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                required>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ old('area_id', $rate->area_id) == $area->id ? 'selected' : '' }}>
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
                            <input type="text" name="vehicle_type" id="vehicle_type"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Masukkan jenis kendaraan"
                                value="{{ old('vehicle_type', $rate->vehicle_type) }}"
                                required>
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
                                value="{{ old('amount', $rate->amount) }}"
                                required>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                    </div>

                    <!-- Button -->
                    <div class="block w-full mt-6">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 font-medium rounded-lg text-sm px-4 py-2">
                            <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Edit Data Tarif Parkir
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</x-app-layout>
