<x-app-layout>
    <x-slot name="title">
        Data Rates
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Data Rates</h5>
                </div>

                <!-- Action & Search -->
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">

                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" action="{{ route('admin.rates.index') }}">
                            <input type="text" name="search" placeholder="Cari rate..."
                                value="{{ request('search') }}"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-l-lg block w-full p-2">
                            <button type="submit"
                                class="text-white bg-primary px-4 py-2 rounded-r-lg">
                                Cari
                            </button>
                        </form>
                    </div>

                    <a href="{{ route('admin.rates.create') }}"
                        class="flex items-center justify-center text-white bg-primary rounded-lg text-sm px-4 py-2">
                        + Tambah Rate
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="p-4">Area</th>
                                <th class="p-4">Jenis Kendaraan</th>
                                <th class="p-4">Tarif</th>
                                <th class="p-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rates as $rate)
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $rate->area->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $rate->vehicle_type }}
                                    </td>
                                    <td class="px-4 py-3">
                                        Rp {{ number_format($rate->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-3">

                                            <a href="{{ route('admin.rates.edit', $rate->id) }}"
                                                class="text-white bg-primary px-3 py-2 rounded-lg text-sm">
                                                Edit
                                            </a>

                                            <form id="rate-destroy-{{ $rate->id }}"
                                                action="{{ route('admin.rates.destroy', $rate->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    onclick="event.preventDefault(); confirmDelete({{ $rate->id }})"
                                                    class="text-red-700 border border-red-700 px-3 py-2 rounded-lg text-sm">
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4">
                    {{ $rates->links() }}
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data rate akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('rate-destroy-' + id).submit();
                }
            })
        }
    </script>
</x-app-layout>
