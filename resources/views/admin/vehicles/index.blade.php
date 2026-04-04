<x-app-layout>
    <x-slot name="title">
        Data Kendaraan
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
                        <form class="flex items-center" action="{{ route('admin.vehicles.index') }}">
                            <label for="simple-search" class="sr-only">Cari</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Cari plat nomor atau warna"
                                    value="{{ request('search') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-0 focus:border-primary block w-full pl-10 p-2">
                            </div>
                            <button type="submit"
                                class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 border border-primary focus:ring-4 focus:ring-primary/20 font-medium rounded-r-lg text-sm px-4 py-2 focus:outline-none">
                                Cari
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                <th class="p-4">Plat Nomor</th>
                                <th class="p-4">Warna</th>
                                <th class="p-4">Tipe</th>
                                <th class="p-4 text-center">Total Transaksi</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicles as $vehicle)
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-mono font-semibold">
                                        {{ $vehicle->plate_number }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $vehicle->color }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $vehicle->type === 'motorcycle'
                                                ? 'bg-blue-100 text-blue-800'
                                                : 'bg-green-100 text-green-800' }}">
                                            {{ $vehicle->type === 'motorcycle' ? 'Motor' : 'Mobil' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $vehicle->transactions_count ?? $vehicle->transactions->count() }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.vehicles.show', $vehicle->id) }}"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/75 focus:ring-4 focus:outline-none focus:ring-primary/20">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                        Tidak ada data kendaraan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4">
                    {{ $vehicles->links() }}
                </div>

            </div>
        </div>
    </section>
</x-app-layout>
