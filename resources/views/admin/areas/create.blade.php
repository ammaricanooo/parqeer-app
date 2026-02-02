<x-app-layout>
    <x-slot name="title">Area</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Tambah Data Area
        </h2>
    </x-slot>

    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-lg p-4">

                <form method="POST" action="{{ route('admin.areas.store') }}">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-4">

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

                    <div class="mt-6">
                        <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm">
                            Tambah Area
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</x-app-layout>
