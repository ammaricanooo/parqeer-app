<x-app-layout>
    <x-slot name="title">Edit Data Area</x-slot>

    <section class="py-6">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-4">
                <form method="POST" action="{{ route('admin.areas.update', $area->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-3">

                        <div>
                            <label class="block mb-2 text-sm font-medium">Nama Area</label>
                            <input type="text" name="name" class="bg-gray-50 border rounded-lg w-full p-2.5"
                                value="{{ old('name', $area->name) }}" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Kapasitas</label>
                            <input type="number" name="capacity" class="bg-gray-50 border rounded-lg w-full p-2.5"
                                value="{{ old('capacity', $area->capacity) }}" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium">Terisi</label>
                            <input type="number" name="occupied" class="bg-gray-50 border rounded-lg w-full p-2.5"
                                value="{{ old('occupied', $area->occupied) }}" required>
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
                            Simpan Data Area
                        </button>
                        <a href="/admin/areas"
                            class="flex items-center justify-center text-white bg-gray-400 hover:bg-gray-400/75 focus:ring-4 focus:ring-gray-400/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
