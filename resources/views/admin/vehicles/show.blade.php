<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Detail Kendaraan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">
                    <div class="mb-6 pb-6 border-b">
                        <h3 class="text-lg font-bold mb-4">Informasi Kendaraan</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Plat Nomor</p>
                                <p class="font-bold">{{ $vehicle->plate_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Warna</p>
                                <p class="font-bold">{{ $vehicle->color }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tipe</p>
                                <p class="font-bold">{{ ucfirst($vehicle->type) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Transaksi</p>
                                <p class="font-bold">{{ $vehicle->transactions->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Transaksi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="text-left bg-gray-100">
                                    <th class="px-4 py-2">ID</th>
                                    <th class="px-4 py-2">Waktu Masuk</th>
                                    <th class="px-4 py-2">Waktu Keluar</th>
                                    <th class="px-4 py-2">Area</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Total</th>
                                    <th class="px-4 py-2">Struk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicle->transactions as $tx)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">{{ $tx->id }}</td>
                                        <td class="px-4 py-2">{{ $tx->entry_time->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2">{{ $tx->exit_time?->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $tx->area->name }}</td>
                                        <td class="px-4 py-2">
                                            @if($tx->exit_time)
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Keluar</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Masih Parkir</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($tx->exit_time)
                                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 flex gap-1">
                                            <a href="{{ route('admin.transaction.entry-receipt', $tx->id) }}" target="_blank" class="text-blue-600 text-sm">Masuk</a>
                                            @if($tx->exit_time)
                                                <a href="{{ route('admin.transaction.receipt', $tx->id) }}" target="_blank" class="text-green-600 text-sm">Keluar</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-center text-gray-600">Tidak ada transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.vehicles.index') }}" class="text-blue-600">&larr; Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
