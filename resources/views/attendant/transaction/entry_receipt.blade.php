<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight no-print">
            {{ __('Struk Kendaraan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">

            {{-- AREA YANG AKAN DI PRINT --}}
            <div id="print-area" class="bg-white  overflow-hidden shadow-sm sm:rounded-lg p-8 border-2 border-dashed border-gray-200 ">
                <div class="text-gray-900 ">

                    {{-- Header Struk --}}
                    <div class="text-center mb-6">
                        <h3 class="font-black text-3xl tracking-tighter uppercase italic text-blue-600">Parqeer</h3>
                        <p class="text-xs uppercase tracking-widest opacity-70">Layanan Parkir Digital</p>
                        <div class="border-b-2 border-black  my-2 w-1/2 mx-auto"></div>
                        <p class="text-[10px] font-mono opacity-60">ID: #{{ $transaction->id }}</p>
                    </div>

                    {{-- Plat Nomor (Fokus Utama) --}}
                    <div class="bg-gray-100  py-4 px-2 rounded-md border-2 border-black  mb-6 text-center">
                        <p class="text-xs uppercase font-semibold mb-1 opacity-70">Nomor Kendaraan</p>
                        <h4 class="text-3xl font-black tracking-widest font-mono italic">
                            {{ $transaction->plate_number }}
                        </h4>
                    </div>

                    {{-- Grid Detail --}}
                    <div class="grid grid-cols-2 gap-y-4 gap-x-6 mb-8 text-sm border-y border-dashed border-gray-300  py-4 font-mono">
                        <div>
                            <p class="text-[10px] uppercase text-gray-500">Warna</p>
                            <p class="font-bold uppercase">{{ $transaction->vehicle_color }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-gray-500">Area Parkir</p>
                            <p class="font-bold uppercase">{{ $transaction->area->name }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] uppercase text-gray-500">Waktu Masuk</p>
                            <p class="font-bold">{{ $transaction->entry_time->format('d M Y | H:i') }}</p>
                        </div>
                    </div>

                    {{-- QR Code Area --}}
                    <div class="text-center">
                        <div id="qrcode" class="flex justify-center mb-4 p-2 bg-white rounded-lg inline-block mx-auto"></div>
                        <p class="text-[11px] font-bold uppercase tracking-tight">Simpan Struk Ini</p>
                        <p class="text-[9px] text-gray-500 italic mt-1">Scan QR ini saat akan membayar</p>
                    </div>

                    {{-- Footer Kecil --}}
                    <div class="mt-8 text-center opacity-40 text-[9px]">
                        <p>Terima kasih atas kepercayaan Anda</p>
                        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
                    </div>

                </div>
            </div>
            {{-- END PRINT AREA --}}

            {{-- BUTTON ACTIONS --}}
            <div class="flex gap-3 justify-center mt-6 no-print">
                <a href="{{ route('attendant.transaction.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-white  border border-gray-300  rounded-md font-semibold text-xs text-gray-700  uppercase tracking-widest shadow-sm hover:bg-gray-50  transition ease-in-out duration-150">
                    ← Kembali
                </a>
                <button id="printBtn" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-blue-500/30">
                    🖨️ Cetak Struk
                </button>
            </div>

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Entry ticket QR akan mengarah ke scan ticket petugas (attendant)
            const qrUrl = '{{ route('attendant.transaction.scan-ticket', $transaction->id) }}';

            // Setup QR Code
            new QRCode(document.getElementById("qrcode"), {
                text: qrUrl,
                width: 140,
                height: 140,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });

            // Trigger Print
            document.getElementById('printBtn').addEventListener('click', () => {
                window.print();
            });
        });
    </script>

    {{-- PRINT STYLE OPTIMIZATION --}}
    <style>
        @media print {
            /* Sembunyikan semua elemen layout Laravel */
            body * { visibility: hidden; background: white !important; }
            nav, .no-print { display: none !important; }

            /* Tampilkan area struk saja */
            #print-area, #print-area * {
                visibility: visible;
            }

            /* Atur posisi struk ke pojok kiri atas kertas thermal */
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm; /* Standar thermal paper */
                padding: 10px;
                border: none !important;
                box-shadow: none !important;
            }

            /* Pastikan QR Code tercetak jelas */
            #qrcode img {
                display: block !important;
                margin: 0 auto;
            }
        }
    </style>
</x-app-layout>