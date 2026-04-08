<x-app-layout>
    <x-slot name="title">
        Edit Tipe Kendaraan
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tipe Kendaraan') }}
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="flex items-center px-4 py-2">
                    <h5 class="text-gray-800 font-semibold">Edit Tipe Kendaraan</h5>
                </div>

                <div class="mx-4 py-4 border-t"></div>

                <form method="POST" action="{{ route('admin.vehicle-types.update', $vehicleType->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-2 md:gap-6">
                        <div>
                            <label for="key" class="block mb-2 text-sm font-medium text-gray-900">Kunci Tipe</label>
                            <input type="text" name="key" id="key"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: car" value="{{ old('key', $vehicleType->key) }}" required>
                            <x-input-error :messages="$errors->get('key')" class="mt-2" />
                        </div>

                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Tipe</label>
                            <input type="text" name="name" id="name"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: Mobil" value="{{ old('name', $vehicleType->name) }}" required>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 w-full mt-4">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.vehicle-types.index') }}"
                            class="flex items-center justify-center text-white bg-gray-400 hover:bg-gray-400/75 focus:ring-4 focus:ring-gray-400/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
