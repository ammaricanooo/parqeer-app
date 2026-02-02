<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Transaksi Parkir') }}
            </h2>
            <a href="{{ route('petugas.transaction.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Kendaraan Masuk
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

            <!-- Kendaraan Parkir Aktif -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">🚗 Kendaraan Parkir Aktif - STATUS MASUK ({{ $activeTransactions->count() }})</h3>

                    @if ($activeTransactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-200 dark:bg-gray-700">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Plat Nomor</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Warna</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Area</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Waktu Masuk</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Durasi</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activeTransactions as $transaction)
                                        @php
                                            $now = now();
                                            $duration = $transaction->entry_time->diffInMinutes($now);
                                            $hours = intval($duration / 60);
                                            $minutes = $duration % 60;
                                        @endphp
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-yellow-400">
                                            <td class="border border-gray-300 px-4 py-2">#{{ $transaction->id }}</td>
                                            <td class="border border-gray-300 px-4 py-2 font-bold">{{ $transaction->plate_number }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->vehicle_color }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->area->name ?? '-' }}</td>
                                            <td class="border border-gray-300 px-4 py-2">
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm font-bold">MASUK</span>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->entry_time->format('d/m/Y H:i') }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $hours }}j {{ $minutes }}m</td>
                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                <a href="{{ route('petugas.transaction.exit', $transaction->id) }}" class="inline-block bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Keluar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada kendaraan yang sedang parkir.</p>
                    @endif
                </div>
            </div>

            <!-- Riwayat Transaksi Hari Ini -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">📋 Riwayat Transaksi Hari Ini - STATUS SUDAH KELUAR ({{ $completedTransactions->count() }})</h3>

                    @if ($completedTransactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300">
                                <thead class="bg-gray-200 dark:bg-gray-700">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Plat Nomor</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Area</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Masuk</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">Keluar</th>
                                        <th class="border border-gray-300 px-4 py-2 text-right">Durasi</th>
                                        <th class="border border-gray-300 px-4 py-2 text-right">Tarif/Jam</th>
                                        <th class="border border-gray-300 px-4 py-2 text-right">Total</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Pembayaran</th>
                                        <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($completedTransactions as $transaction)
                                        @php
                                            $ratePerHour = $transaction->rate->amount;
                                            $durationHours = ceil($transaction->duration_minutes / 60);
                                        @endphp
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 border-l-4 border-green-400">
                                            <td class="border border-gray-300 px-4 py-2">#{{ $transaction->id }}</td>
                                            <td class="border border-gray-300 px-4 py-2 font-bold">{{ $transaction->plate_number }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->area->name ?? '-' }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->entry_time->format('H:i') }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $transaction->exit_time->format('H:i') }}</td>
                                            <td class="border border-gray-300 px-4 py-2 text-right">{{ $transaction->duration_minutes }} menit ({{ $durationHours }}j)</td>
                                            <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($ratePerHour, 0, ',', '.') }}</td>
                                            <td class="border border-gray-300 px-4 py-2 text-right font-bold text-lg">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                @if ($transaction->status === 'paid')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-bold">BAYAR</span>
                                                @else
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-sm font-bold">PENDING</span>
                                                @endif
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2 text-center">
                                                <a href="{{ route('petugas.transaction.receipt', $transaction->id) }}" target="_blank" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Struk
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada transaksi hari ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
