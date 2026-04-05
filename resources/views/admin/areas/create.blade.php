<x-app-layout>
    <x-slot name="title">Area</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Tambah Data Area
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg space-y-3 md:space-y-0 md:space-x-4 p-4">

                <form method="POST" action="{{ route('admin.areas.store') }}">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-3">

                        <div>
                            <label class="block mb-2 text-sm font-medium">Nama Area</label>
                            <input type="text" name="name"
                                class="bg-gray-50 border rounded-lg w-full p-2.5"
                                value="{{ old('name') }}" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Kapasitas</label>
                            <input type="number" name="capacity"
                                class="bg-gray-50 border rounded-lg w-full p-2.5"
                                value="{{ old('capacity') }}" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Terisi</label>
                            <input type="number" name="occupied"
                                class="bg-gray-50 border rounded-lg w-full p-2.5"
                                value="{{ old('occupied', 0) }}" required>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-4 w-full mt-4">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Tambah Data Area Parkir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
