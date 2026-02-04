<x-app-layout>
    <x-slot name="title">
        Transaksi Parkir
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Transaksi Kendaraan') }}
        </h2>
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Data Kendaraan Masuk</h5>
                </div>

                <!-- Action & Search -->
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">

                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" action="{{ route('attendant.transaction.index') }}">
                            <input type="text" name="search" placeholder="Cari kendaraan..."
                                value="{{ request('search') }}"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-l-lg block w-full p-2">
                            <button type="submit"
                                class="text-white bg-primary px-4 py-2 rounded-r-lg">
                                Cari
                            </button>
                        </form>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('attendant.transaction.scan') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm">Scan QR</a>
                        <a href="{{ route('attendant.transaction.create') }}" class="text-white bg-primary px-4 py-2 rounded-lg text-sm">+ Tambah Transaksi</a>
                    </div>
                </div>

                @if ($activeTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                        <th class="p-4">ID</th>
                                        <th class="p-4">Plat Nomor</th>
                                        <th class="p-4">Warna</th>
                                        <th class="p-4">Area</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4">Waktu Masuk</th>
                                        <th class="p-4">Durasi</th>
                                        <th class="p-4">Aksi</th>
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
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-medium">{{ $transaction->id }}</td>
                                    <td class="px-4 py-3">{{ $transaction->plate_number }}</td>
                                    <td class="px-4 py-3">{{ $transaction->vehicle_color }}</td>
                                    <td class="px-4 py-3">{{ $transaction->area->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $transaction->status}}</td>
                                    <td class="px-4 py-3">{{ $transaction->entry_time}}</td>
                                    <td class="px-4 py-3">{{ $hours }}j {{ $minutes }}m</td>
                                    <td class="px-4 py-3 flex gap-2">

                                        <a href="{{ route('attendant.transaction.exit', $transaction->id) }}"
                                            class="bg-primary text-white px-3 py-2 rounded-lg text-sm">
                                            Keluar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                                @else
                        <p class="text-gray-500 m-4 ">Tidak ada kendaraan di dalam hari ini.</p>
                    @endif
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Data Kendaraan Keluar</h5>
                </div>

                <!-- Action & Search -->
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">

                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" action="{{ route('attendant.transaction.index') }}">
                            <input type="text" name="search" placeholder="Cari kendaraan..."
                                value="{{ request('search') }}"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-l-lg block w-full p-2">
                            <button type="submit"
                                class="text-white bg-primary px-4 py-2 rounded-r-lg">
                                Cari
                            </button>
                        </form>
                    </div>
                </div>

                @if ($completedTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                        <th class="p-4">ID</th>
                                        <th class="p-4">Plat Nomor</th>
                                        <th class="p-4">Warna</th>
                                        <th class="p-4">Area</th>
                                        <th class="p-4">Masuk</th>
                                        <th class="p-4">Keluar</th>
                                        <th class="p-4">Durasi</th>
                                        <th class="p-4">Tarif/Jam</th>
                                        <th class="p-4">Total</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4">Pembayaran</th>
                                    </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedTransactions as $transaction)
                                        @php
                                            $ratePerHour = $transaction->rate->amount;
                                            $durationHours = ceil($transaction->duration_minutes / 60);
                                        @endphp
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-medium">{{ $transaction->id }}</td>
                                    <td class="px-4 py-3">{{ $transaction->plate_number }}</td>
                                    <td class="px-4 py-3">{{ $transaction->vehicle_color }}</td>
                                    <td class="px-4 py-3">{{ $transaction->area->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $transaction->entry_time}}</td>
                                    <td class="px-4 py-3">{{ $transaction->exit_time->format('H:i') }}</td>
                                    <td class="px-4 py-3">{{ $transaction->duration_minutes }} menit ({{ $durationHours }}j)</td>
                                    <td class="px-4 py-3">Rp {{ number_format($ratePerHour, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">@if ($transaction->status === 'paid')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-bold">BAYAR</span>
                                                @else
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-sm font-bold">PENDING</span>
                                                @endif</td>
                                    <td class="px-4 py-3">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                        <p class="text-gray-500 m-4 ">Tidak ada kendaraan keluar hari ini.</p>
                    @endif
            </div>
        </div>
    </section>
</x-app-layout>
