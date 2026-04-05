<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Scanner QR - Keluar Kendaraan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 ">
                <div class="p-6">
                    
                    {{-- Header Scan --}}
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100  rounded-full mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 ">Arahkan Kamera ke Struk</h3>
                        {{-- Letakkan di bawah teks "Arahkan Kamera ke Struk" --}}
<div class="mb-4">
    <select id="cameraSelection" class="w-full bg-gray-50  border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
        <option value="">Mencari kamera...</option>
    </select>
</div>
                        <p class="text-sm text-gray-500">Pastikan kode QR berada di dalam kotak area pindai</p>
                    </div>

                    {{-- Kamera / Reader Area --}}
                    <div class="relative rounded-xl overflow-hidden bg-black aspect-square border-4 border-gray-100  shadow-inner">
                        <div id="reader" class="w-full h-full"></div>
                        {{-- Overlay Loading --}}
                        <div id="loading-overlay" class="absolute inset-0 flex items-center justify-center bg-gray-900 text-white z-10">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-white mx-auto mb-2"></div>
                                <p class="text-xs uppercase tracking-widest">Menyiapkan Kamera...</p>
                            </div>
                        </div>
                    </div>

                    {{-- Controls --}}
                    <div class="mt-6 space-y-4">
                        <div id="scanStatus" class="text-center text-sm font-medium py-2 px-4 rounded-lg bg-gray-100  text-gray-600 ">
                            Status: Siap memindai
                        </div>

                        <div class="flex flex-wrap justify-center gap-3">
                            <button id="startCameraBtn" class="flex-1 min-w-[140px] bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-blue-500/30">
                                Buka Kamera
                            </button>
                            <button id="stopCameraBtn" class="flex-1 min-w-[140px] bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200 hidden">
                                Tutup Kamera
                            </button>
                            
                            <label class="flex-1 min-w-[140px] flex items-center justify-center bg-gray-200  hover:bg-gray-300 text-gray-800  font-bold py-3 px-4 rounded-xl cursor-pointer transition duration-200">
                                <span>Upload QR</span>
                                <input type="file" id="qrFile" accept="image/*" class="hidden" />
                            </label>
                        </div>

                        {{-- Manual Input --}}
                        <div class="pt-6 border-t border-gray-100 ">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider text-center mb-3">Atau Input Manual</p>
                            <div class="flex gap-2">
                                <input type="text" id="manualId" placeholder="Contoh: 12345"
                                    class="flex-1 bg-gray-50  border-gray-300  text-gray-900  rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 font-mono" />
                                <button id="goBtn" class="bg-gray-800  text-white  px-6 rounded-xl font-bold hover:opacity-90 transition">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script --}}
    <script src="https://unpkg.com/html5-qrcode@2.4.9/minified/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const readerId = "reader";
            const statusBox = document.getElementById('scanStatus');
            const overlay = document.getElementById('loading-overlay');
            const cameraSelect = document.getElementById('cameraSelection');
            const qrFileInput = document.getElementById('qrFile');
            const goBtn = document.getElementById('goBtn');
            const manualIdInput = document.getElementById('manualId');
            let html5QrCode = new Html5Qrcode(readerId);
            let isCameraRunning = false;
            const scanBaseUrl = `{{ url('attendant/transaction') }}`;

            overlay.classList.add('hidden');

            function updateStatus(message, isError = false) {
                statusBox.textContent = message;
                statusBox.classList.toggle('text-red-600', isError);
                statusBox.classList.toggle('text-gray-600', !isError);
            }

            function redirectToScanTicket(urlOrId) {
                if (!urlOrId) return;
                const url = urlOrId.toString();

                if (url.startsWith('http')) {
                    window.location.href = url;
                    return;
                }

                window.location.href = `${scanBaseUrl}/${encodeURIComponent(url)}/scan-ticket`;
            }

            function handleDecoded(decodedText) {
                updateStatus('QR terdeteksi. Mengarahkan...', false);
                stopCamera().finally(() => {
                    redirectToScanTicket(decodedText);
                });
            }

            async function startCamera() {
                let cameraId = cameraSelect.value;
                if (!cameraId && cameraSelect.options.length > 0) {
                    cameraId = cameraSelect.options[0].value;
                    cameraSelect.value = cameraId;
                }

                if (!cameraId) {
                    return alert('Tidak ada kamera tersedia. Gunakan fitur upload QR.');
                }

                overlay.classList.remove('hidden');
                updateStatus('Menghubungkan kamera...');

                try {
                    const config = {
                        fps: 15,
                        qrbox: { width: 220, height: 220 },
                        aspectRatio: 1.0,
                    };

                    await html5QrCode.start(cameraId, config, (decodedText, _decodedResult) => {
                        handleDecoded(decodedText);
                    });

                    isCameraRunning = true;
                    overlay.classList.add('hidden');
                    document.getElementById('startCameraBtn').classList.add('hidden');
                    document.getElementById('stopCameraBtn').classList.remove('hidden');
                    updateStatus('Status: Memindai aktif...');
                } catch (err) {
                    overlay.classList.add('hidden');
                    console.error(err);
                    updateStatus('Error: Gagal akses kamera. Pastikan izin kamera diberikan.', true);
                }
            }

            async function stopCamera() {
                if (!isCameraRunning) return;
                try {
                    await html5QrCode.stop();
                } catch (e) {
                    console.warn('Stop failed', e);
                }
                isCameraRunning = false;
                document.getElementById('startCameraBtn').classList.remove('hidden');
                document.getElementById('stopCameraBtn').classList.add('hidden');
                updateStatus('Status: Kamera berhenti.');
            }

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraSelect.innerHTML = '<option value="">Pilih kamera...</option>';
                    devices.forEach((device, index) => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        option.text = device.label || `Kamera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    if (devices.length === 1) {
                        cameraSelect.selectedIndex = 1;
                    }
                } else {
                    updateStatus('Tidak ada kamera terdeteksi. Silakan gunakan Upload QR.', true);
                }
            }).catch(err => {
                console.error('Gagal mendapat daftar kamera', err);
                updateStatus('Gagal mendeteksi kamera. Pastikan perangkat mendukung kamera.', true);
            });

            document.getElementById('startCameraBtn').addEventListener('click', startCamera);
            document.getElementById('stopCameraBtn').addEventListener('click', stopCamera);

            qrFileInput.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (isCameraRunning) {
                    await stopCamera();
                }

                updateStatus('Memproses gambar...');

                try {
                    const decoded = await html5QrCode.scanFile(file, true);
                    handleDecoded(decoded);
                } catch (err) {
                    console.error(err);
                    updateStatus('QR tidak ditemukan di gambar ini.', true);
                }
            });

            goBtn.addEventListener('click', function() {
                const id = manualIdInput.value.trim();
                if (!id) return alert('Masukkan ID transaksi');
                redirectToScanTicket(id);
            });
        });
    </script>

    <style>
        /* Membersihkan UI bawaan library agar lebih rapi */
        #reader { border: none !important; }
        #reader video { object-fit: cover !important; border-radius: 0.75rem; }
    </style>
</x-app-layout>