<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Scanner QR - Cari Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div id="reader" style="width:100%"></div>

                    <div class="mt-4 text-center">
                        <div class="flex justify-center gap-2 mb-2">
                            <button id="startCameraBtn" class="bg-green-600 text-white px-4 py-2 rounded">Mulai Kamera</button>
                            <button id="stopCameraBtn" class="bg-red-600 text-white px-4 py-2 rounded hidden">Stop Kamera</button>
                            <label class="bg-gray-200 px-3 py-2 rounded cursor-pointer">
                                <span>Upload Foto QR</span>
                                <input type="file" id="qrFile" accept="image/*" class="hidden" />
                            </label>
                        </div>

                        <div id="scanStatus" class="text-sm text-gray-500 mb-2">Tunggu... atau pilih Upload Foto jika tidak bisa pakai kamera.</div>

                        <p class="text-sm text-gray-500">Atau masukkan ID transaksi secara manual:</p>
                        <div class="flex justify-center gap-2 mt-2">
                            <input type="text" id="manualId" placeholder="ID Transaksi" class="px-3 py-2 border rounded" />
                            <button id="goBtn" class="bg-blue-600 text-white px-4 py-2 rounded">Cari</button>
                        </div>
                    </div>

                    <script src="https://unpkg.com/html5-qrcode@2.4.9/minified/html5-qrcode.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const readerId = "reader";
                            let html5QrCode = new Html5Qrcode(readerId);
                            let isCameraRunning = false;

                            function handleDecoded(decodedText) {
                                // Jika decodedText adalah URL, langsung buka; jika ID, arahkan ke exit route
                                try {
                                    const url = new URL(decodedText, window.location.origin);
                                    window.location.href = decodedText;
                                } catch (e) {
                                    const id = decodedText.trim();
                                    if (id) {
                                        window.location.href = `{{ url('attendant/transaction') }}/${id}/exit`;
                                    }
                                }
                            }

                            async function startCamera() {
                                document.getElementById('scanStatus').textContent = 'Mencari kamera...';
                                try {
                                    const cameras = await Html5Qrcode.getCameras();
                                    if (cameras && cameras.length) {
                                        const cameraId = cameras[0].id;
                                        await html5QrCode.start(cameraId, { fps: 10, qrbox: 250 }, (decodedText) => {
                                            handleDecoded(decodedText);
                                        }, (err) => {
                                            // ignore per-frame errors
                                        });
                                        isCameraRunning = true;
                                        document.getElementById('startCameraBtn').classList.add('hidden');
                                        document.getElementById('stopCameraBtn').classList.remove('hidden');
                                        document.getElementById('scanStatus').textContent = 'Kamera aktif. Arahkan kamera ke QR.';
                                    } else {
                                        document.getElementById('scanStatus').textContent = 'Tidak menemukan kamera pada perangkat ini.';
                                    }
                                } catch (err) {
                                    console.error('Start camera failed', err);
                                    document.getElementById('scanStatus').textContent = 'Gagal membuka kamera: ' + (err.message || err);
                                }
                            }

                            function stopCamera() {
                                if (!isCameraRunning) return;
                                html5QrCode.stop().then(() => {
                                    isCameraRunning = false;
                                    document.getElementById('startCameraBtn').classList.remove('hidden');
                                    document.getElementById('stopCameraBtn').classList.add('hidden');
                                    document.getElementById('scanStatus').textContent = 'Kamera dimatikan.';
                                    // re-create instance to ensure fresh state for file scanning
                                    html5QrCode = new Html5Qrcode(readerId);
                                }).catch((e) => {
                                    console.warn('Stop camera error', e);
                                });
                            }

                            document.getElementById('startCameraBtn').addEventListener('click', startCamera);
                            document.getElementById('stopCameraBtn').addEventListener('click', stopCamera);

                            // File upload scanning
                            document.getElementById('qrFile').addEventListener('change', async function(e) {
                                const file = e.target.files && e.target.files[0];
                                if (!file) return;
                                document.getElementById('scanStatus').textContent = 'Memindai gambar...';
                                try {
                                    // Prefer v2 if available
                                    if (typeof html5QrCode.scanFileV2 === 'function') {
                                        const result = await html5QrCode.scanFileV2(file, true);
                                        const decoded = result && (result.decodedText || result);
                                        if (decoded) handleDecoded(decoded);
                                    } else if (typeof html5QrCode.scanFile === 'function') {
                                        const decoded = await html5QrCode.scanFile(file, true);
                                        if (decoded) handleDecoded(decoded);
                                    } else {
                                        document.getElementById('scanStatus').textContent = 'Pemindai tidak mendukung scan file, gunakan manual.';
                                    }
                                } catch (err) {
                                    console.error('Scan file failed', err);
                                    document.getElementById('scanStatus').textContent = 'Gagal memindai gambar: ' + (err.message || err);
                                }
                            });

                            // Coba otomatis mulai kamera (jika pengguna memberi izin)
                            startCamera();

                            document.getElementById('goBtn').addEventListener('click', function() {
                                const id = document.getElementById('manualId').value.trim();
                                if (!id) return alert('Masukkan ID transaksi');
                                window.location.href = `{{ url('attendant/transaction') }}/${id}/exit`;
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>