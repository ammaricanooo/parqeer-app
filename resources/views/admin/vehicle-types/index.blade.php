<x-app-layout>
    <x-slot name="title">
        Data Tipe Kendaraan
    </x-slot>

    <section class="py-6">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg overflow-hidden">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Data Tipe Kendaraan</h5>
                    <a href="{{ route('admin.vehicle-types.create') }}"
                        class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                        Tambah Tipe Kendaraan
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="p-4">Kunci</th>
                                <th class="p-4">Nama Tipe</th>
                                <th class="p-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicleTypes as $type)
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $type->key }}</td>
                                    <td class="px-4 py-3">{{ $type->name }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('admin.vehicle-types.edit', $type->id) }}"
                                                class="py-2 px-3 flex items-center text-sm font-medium text-center text-white bg-primary rounded-lg hover:bg-primary/75 focus:ring-4 focus:outline-none focus:ring-primary/20">
                                                Edit
                                            </a>
                                            <form id="vehicle-type-destroy-{{ $type->id }}"
                                                action="{{ route('admin.vehicle-types.destroy', $type->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center"
                                                    onclick="event.preventDefault(); confirmDelete({{ $type->id }})">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada tipe kendaraan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $vehicleTypes->links() }}
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDelete(id) {
            if (confirm('Apakah kamu yakin ingin menghapus tipe kendaraan ini?')) {
                document.getElementById('vehicle-type-destroy-' + id).submit();
            }
        }
    </script>
</x-app-layout>
