<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Parkir - Parqeer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: radial-gradient(circle at top right, #4f46e5, #312e81);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .checking-status {
            animation: pulse-soft 2s infinite;
        }
    </style>
</head>
<body class="antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="glass-card rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">
            <div class="bg-indigo-600 px-6 py-10 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                    <svg viewBox="0 0 100 100" class="w-full h-full"><circle cx="50" cy="50" r="40" fill="white"/></svg>
                </div>
                <h1 class="text-4xl font-black text-white italic tracking-tighter mb-1">PARQEER</h1>
                <p class="text-indigo-100 text-xs font-bold uppercase tracking-[0.3em]">Payment Gateway</p>
            </div>

            <div class="p-8">
                <div class="flex justify-center -mt-14 mb-8">
                    <div class="bg-white px-8 py-3 rounded-2xl shadow-xl border border-gray-100 text-center">
                        <p class="text-[10px] font-bold text-indigo-500 uppercase">Nomor Kendaraan</p>
                        <p class="text-2xl font-black text-gray-900 tracking-wider">{{ $transaction->plate_number }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Durasi</p>
                        <p class="font-black text-gray-800">{{ $hours }}j {{ $minutes }}m</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Area</p>
                        <p class="font-black text-gray-800">{{ $transaction->area->name }}</p>
                    </div>
                </div>

                <div class="mb-8 text-center">
                    <p class="text-sm font-bold text-gray-500 mb-1">Total Biaya</p>
                    <h2 class="text-5xl font-black text-gray-900 tracking-tighter">
                        <span class="text-2xl font-bold mr-1">Rp</span>{{ number_format($transaction->amount, 0, ',', '.') }}
                    </h2>
                </div>

                <div class="space-y-4">
                    @if($paymentGatewayUrl)
                        <a href="{{ $paymentGatewayUrl }}" target="_blank" 
                           class="group w-full flex items-center justify-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-5 px-6 rounded-3xl transition-all shadow-lg shadow-indigo-200 active:scale-95">
                            <span class="text-xl">💳</span>
                            <span>BAYAR SEKARANG</span>
                        </a>
                    @endif

                    <button onclick="checkPaymentStatus()" id="checkBtn"
                            class="w-full flex items-center justify-center gap-3 bg-white border-2 border-gray-100 hover:border-indigo-100 text-gray-600 font-bold py-4 px-6 rounded-3xl transition-all active:bg-gray-50">
                        <span id="checkIcon" class="inline-block">🔄</span>
                        <span id="checkText">CEK STATUS</span>
                    </button>
                </div>

                <div class="mt-8 p-4 bg-blue-50/50 rounded-2xl flex items-start gap-3">
                    <span class="text-lg">💡</span>
                    <p class="text-[11px] leading-relaxed text-blue-800 font-medium">
                        Sistem memantau pembayaran secara otomatis. Anda tidak perlu menutup halaman ini sampai status berubah menjadi lunas.
                    </p>
                </div>
            </div>

            <div class="bg-gray-50/80 px-8 py-5 border-t border-gray-100 flex justify-between items-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID: #{{ $transaction->id }}</span>
                <div id="statusIndicator" class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-black text-amber-600 uppercase">Menunggu Pembayaran</span>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-white/60 text-xs font-medium">
            Parqeer Payment System &bull; 2026
        </p>
    </div>

    <script>
        let checkCount = 0;
        const maxChecks = 30; // 30 kali check (sekitar 5 menit)
        const btn = document.getElementById('checkBtn');
        const btnText = document.getElementById('checkText');
        const btnIcon = document.getElementById('checkIcon');

        function updateUI(status) {
            if (status === 'paid' || status === 'out') {
                const indicator = document.getElementById('statusIndicator');
                indicator.innerHTML = `
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-[10px] font-black text-green-600 uppercase tracking-widest text-sm">LUNAS</span>
                `;
                
                // Success State on Button
                btn.className = "w-full flex items-center justify-center gap-3 bg-green-600 text-white font-black py-4 px-6 rounded-3xl transition-all";
                btnText.innerText = "PEMBAYARAN BERHASIL!";
                btnIcon.innerText = "✅";

                setTimeout(() => {
                    window.location.href = `/payment/success?order_id=TXN-{{ $transaction->id }}`;
                }, 1500);
            }
        }

        async function checkPaymentStatus() {
            if (checkCount >= maxChecks) return;

            // Visual feedback
            btnIcon.classList.add('animate-spin');
            btnText.innerText = "MEMERIKSA...";

            try {
                const response = await fetch(`/attendant/transaction/{{ $transaction->id }}/current-amount`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.status === 'paid' || data.status === 'out') {
                    updateUI(data.status);
                } else {
                    checkCount++;
                    // Reset button if manual check
                    setTimeout(() => {
                        btnIcon.classList.remove('animate-spin');
                        btnText.innerText = "CEK STATUS";
                    }, 1000);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Auto polling every 10s
        const autoCheck = setInterval(() => {
            if (checkCount < maxChecks) {
                checkPaymentStatus();
            } else {
                clearInterval(autoCheck);
            }
        }, 10000);
    </script>
</body>
</html>