<x-app-layout>
    <x-slot name="title">
        Edit Data Tarif Parqeer
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Tarif Parqeer') }}
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg space-y-3 md:space-y-0 md:space-x-4 p-4">

                <!-- Title -->
                <div class="flex items-center px-4 py-2">
                    <h5 class="text-gray-800 font-semibold">Edit Data Tarif Parkir</h5>
                </div>

                <div class="mx-4 py-4 border-t"></div>

                <!-- Form -->
                <form method="POST" action="{{ route('admin.rates.update', $rate->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 md:gap-6">

                        <!-- Area -->
                        <div>
                            <label for="area_id" class="block mb-2 text-sm font-medium text-gray-900">
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
                            <label for="vehicle_type" class="block mb-2 text-sm font-medium text-gray-900">
                                Jenis Kendaraan
                            </label>
                            <select name="vehicle_type" id="vehicle_type"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                required>
                                <option value="car" {{ old('vehicle_type', $rate->vehicle_type) === 'car' ? 'selected' : '' }}>Mobil</option>
                                <option value="motorcycle" {{ old('vehicle_type', $rate->vehicle_type) === 'motorcycle' ? 'selected' : '' }}>Motor</option>
                            </select>
                            <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">
                                Tarif
                            </label>
                            <input type="number" name="amount" id="amount"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Masukkan tarif" value="{{ old('amount', $rate->amount) }}" required>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        
                        <!-- Pricing Type -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tipe Harga</label>
                            <div class="flex gap-3">
                                <label class="flex-1 p-3 border rounded-xl cursor-pointer bg-white">
                                    <input type="radio" name="pricing_type" value="per_hour"
                                        class="mr-2" {{ old('pricing_type', $rate->pricing_type) === 'per_hour' ? 'checked' : '' }}>
                                    Per Jam
                                </label>
                                <label class="flex-1 p-3 border rounded-xl cursor-pointer bg-white">
                                    <input type="radio" name="pricing_type" value="fixed"
                                        class="mr-2" {{ old('pricing_type', $rate->pricing_type) === 'fixed' ? 'checked' : '' }}>
                                    Harga Tetap
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('pricing_type')" class="mt-2" />
                        </div>


                    </div>
                    <div class="flex items-center justify-end gap-4 w-full mt-4">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                class="h-5 w-5 mr-2 -ml-0.5">
                                <path fill="currentColor"
                                    d="M21 7v12q0 .825-.587 1.413T19 21H5q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h12zm-2 .85L16.15 5H5v14h14zM12 18q1.25 0 2.125-.875T15 15t-.875-2.125T12 12t-2.125.875T9 15t.875 2.125T12 18m-6-8h9V6H6zM5 7.85V19V5z" />
                            </svg>
                            Simpan Data Tarif
                        </button>
                        <a href="/admin/rates"
                            class="flex items-center justify-center text-white bg-gray-400 hover:bg-gray-400/75 focus:ring-4 focus:ring-gray-400/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
