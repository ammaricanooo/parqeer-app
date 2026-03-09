<x-app-layout>
    <x-slot name="title">
        Transaksi Parkir
    </x-slot>

    <section class="pt-12 pb-6">

        <!-- ===================== KENDARAAN MASUK ===================== -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Data Kendaraan Masuk</h5>
                </div>

                <!-- Action & Search -->
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">

                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" action="{{ route('attendant.transaction.index') }}">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari kendaraan..."
                                    class="bg-gray-50 border border-gray-300 text-sm rounded-l-lg block w-full pl-10 p-2">
                            </div>
                            <button type="submit" class="text-white bg-primary px-4 py-2 rounded-r-lg">
                                Cari
                            </button>
                        </form>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('attendant.transaction.scan') }}"
                            class="flex items-center justify-center text-white bg-yellow-500 hover:bg-yellow-600 font-medium rounded-lg text-sm px-4 py-2">
                            Scan QR
                        </a>
                        <a href="{{ route('attendant.transaction.create') }}"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Tambah Transaksi
                        </a>
                    </div>
                </div>

                @if ($activeTransactions->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-gray-50">
                                <tr>
                                    <th class="p-4">ID</th>
                                    <th class="p-4">Plat</th>
                                    <th class="p-4">Warna</th>
                                    <th class="p-4">Area</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Masuk</th>
                                    <th class="p-4">Durasi</th>
                                    <th class="p-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeTransactions as $transaction)
                                    @php
                                        $minutes = $transaction->entry_time->diffInMinutes(now());
                                        $hours = intdiv($minutes, 60);
                                        $remain = $minutes % 60;
                                    @endphp
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="px-4 py-3 font-medium">{{ $transaction->id }}</td>
                                        <td class="px-4 py-3">{{ $transaction->plate_number }}</td>
                                        <td class="px-4 py-3">{{ $transaction->vehicle_color }}</td>
                                        <td class="px-4 py-3">{{ $transaction->area->name ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                                                {{ strtoupper($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-nowrap">{{ $transaction->entry_time }}</td>
                                        <td class="px-4 py-3">{{ $hours }}j {{ $remain }}m</td>
                                        <td class="px-4 py-3 flex gap-2">
                                            <a href="{{ route('attendant.transaction.entry-receipt', $transaction->id) }}"
                                                target="_blank"
                                                class="px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/75">
                                                Struk
                                            </a>
                                            <a href="{{ route('attendant.transaction.exit', $transaction->id) }}"
                                                class="px-3 py-2 text-sm font-medium text-red-700 border border-red-700 rounded-lg hover:bg-red-700 hover:text-white">
                                                Keluar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="m-4 text-gray-500">Tidak ada kendaraan di dalam.</p>
                @endif
            </div>
        </div>

        <!-- ===================== KENDARAAN KELUAR ===================== -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden">

                <div class="p-4">
                    <h5 class="text-gray-800 font-semibold">Data Kendaraan Keluar</h5>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                <th class="p-4">ID</th>
                                <th class="p-4">Plat</th>
                                <th class="p-4">Warna</th>
                                <th class="p-4">Area</th>
                                <th class="p-4">Masuk</th>
                                <th class="p-4">Keluar</th>
                                <th class="p-4">Durasi</th>
                                <th class="p-4">Tarif/Jam</th>
                                <th class="p-4">Total</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Struk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($completedTransactions as $transaction)
                                @php
                                    $rate = $transaction->rate->amount;
                                    $hours = ceil($transaction->duration_minutes / 60);
                                @endphp
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-3 font-medium">{{ $transaction->id }}</td>
                                    <td class="px-4 py-3">{{ $transaction->plate_number }}</td>
                                    <td class="px-4 py-3">{{ $transaction->vehicle_color }}</td>
                                    <td class="px-4 py-3">{{ $transaction->area->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-nowrap">{{ $transaction->entry_time }}</td>
                                    <td class="px-4 py-3">{{ $transaction->exit_time->format('H:i') }}</td>
                                    <td class="px-4 py-3">{{ $transaction->duration_minutes }} m
                                        ({{ $hours }}j)</td>
                                    <td class="px-4 py-3">Rp {{ number_format($rate, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ strtoupper($transaction->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 flex gap-1">
                                        <a href="{{ route('attendant.transaction.entry-receipt', $transaction->id) }}"
                                            target="_blank" class="text-blue-600 font-semibold text-sm">Masuk</a>
                                        <span>|</span>
                                        <a href="{{ route('attendant.transaction.receipt', $transaction->id) }}"
                                            target="_blank" class="text-red-600 font-semibold text-sm">Keluar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-6 text-center text-gray-500">
                                        Tidak ada kendaraan keluar
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </section>
</x-app-layout>
