<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Kendaraan - ' . $vehicle->plate_number) }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('petugas.vehicle.edit', $vehicle->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('petugas.vehicle.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Kendaraan -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Informasi Kendaraan</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Plat Nomor</p>
                            <p class="font-bold text-lg">{{ $vehicle->plate_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Warna</p>
                            <p class="font-bold">{{ $vehicle->color }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tipe</p>
                            <p class="font-bold capitalize">{{ $vehicle->type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Terdaftar Sejak</p>
                            <p class="font-bold">{{ $vehicle->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Transaksi -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Riwayat Transaksi</h3>

                    @if ($vehicle->transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-200 dark:bg-gray-700">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Area</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Masuk</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Keluar</th>
                                        <th class="border border-gray-300 px-4 py-2 text-right">Durasi</th>
                                        <th class="border border-gray-300 px-4 py-2 text-right">Tarif</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vehicle->transactions->sortByDesc('created_at') as $transaction)
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="border border-gray-300 px-4 py-2">#{{ $transaction->id }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->area->name }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->entry_time->format('d/m/Y H:i') }}</td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                @if ($transaction->exit_time)
                                                    {{ $transaction->exit_time->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-yellow-600 font-bold">Masih Parkir</span>
                                                @endif
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-right">
                                                @if ($transaction->duration_minutes)
                                                    {{ $transaction->duration_minutes }} menit
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-right font-bold">
                                                @if ($transaction->amount)
                                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                @if ($transaction->exit_time === null)
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">Aktif</span>
                                                @elseif ($transaction->status === 'paid')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Bayar</span>
                                                @else
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-sm">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada transaksi untuk kendaraan ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
