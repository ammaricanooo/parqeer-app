<x-app-layout>
    <x-slot name="title">
        Data Kendaraan
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Kendaraan') }}
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Data Kendaraan</h5>
                </div>

                <!-- Action & Search -->
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">

                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" action="{{ route('attendant.vehicles.index') }}">
                            <input type="text" name="search" placeholder="Cari kendaraan..."
                                value="{{ request('search') }}"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-l-lg block w-full p-2">
                            <button type="submit"
                                class="text-white bg-primary px-4 py-2 rounded-r-lg">
                                Cari
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('attendant.vehicles.create') }}"
                        class="text-white bg-primary px-4 py-2 rounded-lg text-sm">
                        + Tambah Kendaraan
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                <th class="p-4">Plat Nomor</th>
                                <th class="p-4">Warna</th>
                                <th class="p-4">Tipe</th>
                                <th class="p-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicles as $vehicle)
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-medium">{{ $vehicle->plate_number }}</td>
                                    <td class="px-4 py-3">{{ $vehicle->color }}</td>
                                    <td class="px-4 py-3">{{ $vehicle->type }}</td>
                                    <td class="px-4 py-3 flex gap-2">

                                        <a href="{{ route('attendant.vehicles.edit', $vehicle->id) }}"
                                            class="bg-primary text-white px-3 py-2 rounded-lg text-sm">
                                            Edit
                                        </a>

                                        <form id="vehicle-destroy-{{ $vehicle->id }}"
                                            action="{{ route('attendant.vehicles.destroy', $vehicle->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                onclick="event.preventDefault(); confirmDelete({{ $vehicle->id }})"
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
                    {{ $vehicles->links() }}
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin hapus kendaraan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('vehicle-destroy-' + id).submit();
                }
            })
        }
    </script>
</x-app-layout>

