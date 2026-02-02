<x-app-layout>
    <x-slot name="title">
        Data Areas
    </x-slot>

    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Data areas</h5>
                </div>

                <!-- Action & Search -->
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">

                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" action="{{ route('admin.areas.index') }}">
                            <input type="text" name="search" placeholder="Cari area..."
                                value="{{ request('search') }}"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-l-lg block w-full p-2">
                            <button type="submit"
                                class="text-white bg-primary px-4 py-2 rounded-r-lg">
                                Cari
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('admin.areas.create') }}"
                        class="text-white bg-primary px-4 py-2 rounded-lg text-sm">
                        + Tambah Area
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                <th class="p-4">Nama Area</th>
                                <th class="p-4">Kapasitas</th>
                                <th class="p-4">Terisi</th>
                                <th class="p-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areas as $area)
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-medium">{{ $area->name }}</td>
                                    <td class="px-4 py-3">{{ $area->capacity }}</td>
                                    <td class="px-4 py-3">{{ $area->occupied }}</td>
                                    <td class="px-4 py-3 flex gap-2">

                                        <a href="{{ route('admin.areas.edit', $area->id) }}"
                                            class="bg-primary text-white px-3 py-2 rounded-lg text-sm">
                                            Edit
                                        </a>

                                        <form id="area-destroy-{{ $area->id }}"
                                            action="{{ route('admin.areas.destroy', $area->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                onclick="event.preventDefault(); confirmDelete({{ $area->id }})"
                                                class="border border-red-600 text-red-600 px-3 py-2 rounded-lg text-sm">
                                                Hapus
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $areas->links() }}
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin hapus area?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('area-destroy-' + id).submit();
                }
            })
        }
    </script>
</x-app-layout>
