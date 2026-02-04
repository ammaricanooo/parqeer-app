<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Struk Kendaraan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="text-center mb-4">
                        <h3 class="font-bold text-lg">Parqeer</h3>
                        <p class="text-sm text-gray-600">Struk Kendaraan Masuk</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm">ID Transaksi</p>
                        <p class="font-bold text-lg">{{ $transaction->id }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Plat</p>
                            <p class="font-bold">{{ $transaction->plate_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Warna</p>
                            <p class="font-bold">{{ $transaction->vehicle_color }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Area</p>
                            <p class="font-bold">{{ $transaction->area->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Waktu Masuk</p>
                            <p class="font-bold">{{ $transaction->entry_time->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <div id="qrcode"></div>
                        <p class="text-xs text-gray-500 mt-2">Tunjukkan QR ini saat keluar supaya petugas bisa cepat menemukan transaksi</p>
                    </div>

                    <div class="flex gap-2 justify-center">
                        <button id="printBtn" class="bg-blue-600 text-white px-4 py-2 rounded">Cetak Struk</button>
                        <a href="{{ route('attendant.transaction.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Selesai</a>
                    </div>

                    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const qrcodeContainer = document.getElementById('qrcode');
                            const txUrl = '{{ route('attendant.transaction.exit', $transaction->id) }}';

                            // Generate QR yang berisi URL ke halaman exit transaksi (petugas akan diarahkan ke halaman exit saat scan)
                            new QRCode(qrcodeContainer, {
                                text: txUrl,
                                width: 160,
                                height: 160,
                                colorDark: "#000000",
                                colorLight: "#ffffff",
                            });

                            document.getElementById('printBtn').addEventListener('click', function () {
                                window.print();
                            });
                        });
                    </script>

                    <style>
                        @media print {
                            body * { visibility: hidden; }
                            #qrcode, #qrcode * { visibility: visible; }
                            #qrcode { position: absolute; left: 0; top: 0; }
                        }
                    </style>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>