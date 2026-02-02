<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Input Kendaraan Keluar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Info Kendaraan -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <h3 class="font-bold mb-2">Informasi Kendaraan</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Plat Nomor</p>
                                <p class="font-bold text-lg">{{ $transaction->plate_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Warna</p>
                                <p class="font-bold">{{ $transaction->vehicle_color }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Area</p>
                                <p class="font-bold">{{ $transaction->area->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Waktu Masuk</p>
                                <p class="font-bold">{{ $transaction->entry_time->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('petugas.transaction.exit.process', $transaction->id) }}" method="POST">
                        @csrf

                        <!-- Waktu Keluar -->
                        <div class="mb-6">
                            <label for="exit_time" class="block text-sm font-medium mb-1">
                                Waktu Keluar <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="exit_time" name="exit_time" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                required
                                value="{{ old('exit_time', now()->format('Y-m-d\TH:i')) }}">
                            @error('exit_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Waktu harus setelah atau sama dengan waktu masuk</p>
                        </div>

                        <!-- Tombol -->
                        <div class="flex gap-2">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Proses Keluar & Hitung Tarif
                            </button>
                            <a href="{{ route('petugas.transaction.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
