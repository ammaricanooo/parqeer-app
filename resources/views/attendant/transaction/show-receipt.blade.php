<x-app-layout>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-4 sm:p-8">
        <div class="max-w-7xl mx-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-4xl font-black text-gray-800 dark:text-white tracking-tight italic">
                        PARQEER<span class="text-blue-600">.OS</span>
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Terminal Kasir Parkir v2.0</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('attendant.transaction.entry-receipt', $transaction->id) }}" target="_blank" 
                       class="px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                        <span>📄 Lihat Struk</span>
                    </a>
                    <a href="{{ route('attendant.transaction.index') }}" 
                       class="px-6 py-3 bg-gray-800 dark:bg-gray-700 rounded-2xl font-bold text-white hover:bg-gray-900 transition-all shadow-lg">
                        ← Kembali
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border-b-8 border-blue-600 overflow-hidden relative">
                        <div class="absolute top-0 right-0 p-8 opacity-10">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
                        </div>
                        
                        <span class="text-blue-600 font-black tracking-widest text-sm uppercase mb-2 block">Data Kendaraan</span>
                        <h2 class="text-8xl md:text-[10rem] font-black tracking-tighter text-gray-900 dark:text-white leading-none mb-6">
                            {{ $transaction->plate_number }}
                        </h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 border-t dark:border-gray-700 pt-8 mt-4">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Durasi Parkir</p>
                                <p class="text-2xl font-black text-gray-800 dark:text-gray-200 tracking-tight">
                                    {{ $transaction->status === 'paid' ? $transaction->duration_minutes : $transaction->calculatePayment()['duration_minutes'] }} <span class="text-sm font-normal text-gray-500 uppercase">Menit</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Area Lokasi</p>
                                <p class="text-2xl font-black text-gray-800 dark:text-gray-200 tracking-tight">{{ $transaction->area->name }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs font-bold text-gray-400 uppercase">Waktu Masuk</p>
                                <p class="text-2xl font-black text-gray-800 dark:text-gray-200 tracking-tight">{{ $transaction->entry_time->format('H:i') }} <span class="text-sm font-medium opacity-50">{{ $transaction->entry_time->format('d M Y') }}</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-600 rounded-[2rem] p-8 text-white flex flex-col md:flex-row justify-between items-center shadow-xl shadow-blue-200 dark:shadow-none">
                        <div>
                            <p class="text-blue-100 font-bold uppercase tracking-widest text-sm mb-1">Total Tagihan</p>
                            <p class="text-7xl font-black leading-none">
                                Rp {{ number_format($transaction->status === 'paid' ? $transaction->amount : $transaction->calculatePayment()['amount'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="mt-6 md:mt-0 text-right">
                            <span class="px-6 py-2 bg-white/20 backdrop-blur-md rounded-full font-black text-lg uppercase tracking-tighter">
                                {{ $transaction->status === 'paid' ? 'Sudah Lunas' : 'Menunggu Bayar' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    @php
                        $isPaid = $transaction->status === 'paid' || $transaction->status === 'out';
                        $amount = $isPaid ? $transaction->amount : $transaction->calculatePayment()['amount'];
                    @endphp

                    @if(!$isPaid)
                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-8 border border-gray-100 dark:border-gray-700 h-full">
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-8 italic">Form Pembayaran Tunai</h3>

                            <form action="{{ route('attendant.transaction.pay', $transaction->id) }}" method="POST" class="flex flex-col gap-6">
                                @csrf
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase mb-3">Jumlah yang Dibayar</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-2xl text-gray-300">Rp</span>
                                        <input type="number" name="paid_amount" id="paid_amount" 
                                            class="w-full pl-16 pr-4 py-6 bg-gray-50 dark:bg-gray-900 border-none rounded-3xl text-3xl font-black focus:ring-4 focus:ring-blue-500/20"
                                            value="{{ $amount }}" min="{{ $amount }}" step="1000" required autofocus>
                                    </div>
                                </div>

                                <div id="change-card" class="p-6 bg-green-50 dark:bg-green-900/20 rounded-3xl border-2 border-dashed border-green-200 dark:border-green-800 hidden">
                                    <p class="text-xs font-black text-green-600 uppercase mb-1">Kembalian</p>
                                    <p id="change-text" class="text-4xl font-black text-green-700 dark:text-green-400 leading-none">Rp 0</p>
                                </div>

                                <button type="submit" class="w-full py-6 bg-gray-900 dark:bg-blue-600 text-white rounded-3xl font-black text-xl hover:scale-[1.02] active:scale-95 transition-all shadow-xl">
                                    SELESAIKAN BAYAR dan KELUAR
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl p-10 flex flex-col items-center justify-center text-center h-full border-4 border-green-500">
                            <div class="w-24 h-24 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-4 italic">TRANSAKSI SELESAI</h2>
                            <p class="text-gray-500 mb-8 font-medium italic">Kendaraan diperbolehkan keluar dari area parkir.</p>
                            
                            <a href="{{ route('attendant.transaction.index') }}" class="w-full py-5 bg-green-600 text-white rounded-3xl font-black text-xl hover:bg-green-700 transition-all">
                                KEMBALI KE BERANDA
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountTotal = {{ (int)$amount }};
            const paidInput = document.getElementById('paid_amount');
            const changeCard = document.getElementById('change-card');
            const changeText = document.getElementById('change-text');

            paidInput?.addEventListener('input', (e) => {
                const val = parseInt(e.target.value) || 0;
                if (val > amountTotal) {
                    const diff = val - amountTotal;
                    changeText.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(diff);
                    changeCard.classList.remove('hidden');
                } else {
                    changeCard.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>