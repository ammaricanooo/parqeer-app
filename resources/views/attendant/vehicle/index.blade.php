<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Kendaraan') }}
            </h2>
            <a href="{{ route('petugas.vehicle.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Tambah Kendaraan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ $message }}
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ $message }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($vehicles->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-200 dark:bg-gray-700">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Plat Nomor</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Warna</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Tipe</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Jumlah Transaksi</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Terdaftar</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vehicles as $vehicle)
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="border border-gray-300 px-4 py-2 font-bold">{{ $vehicle->plate_number }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $vehicle->color }}</td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm capitalize">
                                                    {{ $vehicle->type }}
                                                </span>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $vehicle->transactions_count ?? 0 }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $vehicle->created_at->format('d/m/Y') }}</td>
                                            <td class="border border-gray-300 px-4 py-2 text-center space-x-1">
                                                <a href="{{ route('petugas.vehicle.show', $vehicle->id) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">
                                                    Lihat
                                                </a>
                                                <a href="{{ route('petugas.vehicle.edit', $vehicle->id) }}" class="inline-block bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-sm">
                                                    Edit
                                                </a>
                                                <form action="{{ route('petugas.vehicle.destroy', $vehicle->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm" onclick="return confirm('Yakin hapus?')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $vehicles->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada kendaraan terdaftar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
