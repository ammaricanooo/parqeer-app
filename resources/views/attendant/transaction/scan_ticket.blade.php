<x-app-layout>
    <div class="min-h-screen bg-slate-100 p-4 lg:p-8">
        <div class="max-w-[1400px] mx-auto">
            
            <div class="flex justify-between items-center mb-6 bg-white  p-5 rounded-3xl shadow-sm border border-gray-200 ">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 text-white font-black italic text-xl">
                        P
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900  tracking-tight leading-none uppercase">Gate <span class="text-indigo-600">Scanner</span></h1>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic">Transaction Verification System</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('attendant.transaction.index') }}" class="px-6 py-2.5 bg-gray-100  text-gray-600  font-bold rounded-xl hover:bg-gray-200 transition-all text-sm">
                        Batal
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white  rounded-[2.5rem] shadow-sm border border-gray-200  p-10 relative overflow-hidden flex flex-col justify-center min-h-[350px]">
                        <div class="absolute -right-16 -bottom-16 opacity-[0.03] pointer-events-none">
                            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/></svg>
                        </div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-600 font-black text-[10px] uppercase tracking-widest rounded-lg">ID #{{ $transaction->id }}</span>
                                <span class="px-3 py-1 bg-{{ $transaction->status === 'in' ? 'amber' : 'green' }}-100 dark:bg-{{ $transaction->status === 'in' ? 'amber' : 'green' }}-900/40 text-{{ $transaction->status === 'in' ? 'amber' : 'green' }}-600 dark:text-{{ $transaction->status === 'in' ? 'amber' : 'green' }}-400 font-black text-[10px] uppercase tracking-widest rounded-lg">
                                    {{ $transaction->status }}
                                </span>
                            </div>
                            
                            <h2 class="text-8xl md:text-[10rem] font-black tracking-tighter text-gray-900  leading-none">
                                {{ $transaction->plate_number }}
                            </h2>
                            <p class="text-2xl font-bold text-gray-400 mt-4 italic uppercase tracking-tighter">{{ $transaction->vehicle_color ?? 'No Color Data' }} — {{ $transaction->area->name }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white  p-8 rounded-[2rem] border border-gray-200  shadow-sm">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Waktu Masuk</p>
                            <p class="text-3xl font-black text-gray-900  leading-none tracking-tight">{{ $transaction->entry_time->format('H:i') }}</p>
                            <p class="text-sm font-bold text-gray-400 mt-1 uppercase">{{ $transaction->entry_time->format('d M Y') }}</p>
                        </div>
                        <div class="bg-white  p-8 rounded-[2rem] border border-gray-200  shadow-sm flex flex-col justify-center">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Durasi Parkir</p>
                            <p class="text-3xl font-black text-indigo-600 leading-none tracking-tight">
                                {{ $transaction->entry_time->diff(now())->format('%h Jam %i Menit') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="bg-indigo-600 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-indigo-500/20 sticky top-8">
                        <h3 class="text-2xl font-black italic tracking-tight mb-8 uppercase">Proses Keluar</h3>

                        @if(isset($message) || isset($error))
                            <div class="mb-6 p-4 rounded-2xl font-bold text-sm {{ isset($error) ? 'bg-red-500 text-white' : 'bg-white/20 text-white' }}">
                                {{ $message ?? $error }}
                            </div>
                        @endif

                        @if($status === 'in')
                            <div class="mb-10">
                                <p class="text-indigo-200 font-bold uppercase tracking-widest text-xs mb-1">Total Tagihan</p>
                                <h4 class="text-7xl font-black tracking-tighter">
                                    <span class="text-2xl opacity-60">Rp</span> {{ number_format($paymentInfo['amount'], 0, ',', '.') }}
                                </h4>
                            </div>

                            <form method="POST" action="{{ route('attendant.transaction.pay-and-exit', $transaction->id) }}" class="space-y-6">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em] mb-3 ml-2">Uang Diterima (F10)</label>
                                    <div class="relative">
                                        <span class="absolute left-6 top-1/2 -translate-y-1/2 font-black text-2xl text-indigo-400">Rp</span>
                                        <input type="number" id="paid_amount" name="paid_amount" 
                                            min="{{ $paymentInfo['amount'] }}" 
                                            value="{{ $paymentInfo['amount'] }}" 
                                            class="w-full bg-white text-gray-900 border-none rounded-3xl py-7 pl-16 pr-8 text-4xl font-black focus:ring-8 focus:ring-white/10 shadow-inner"
                                            required autofocus />
                                    </div>
                                </div>

                                <div id="change_card" class="hidden p-8 bg-black/20 rounded-[2rem] border-2 border-dashed border-indigo-400/50 flex flex-col items-center justify-center text-center animate-pulse">
                                    <span class="text-[10px] font-black uppercase text-indigo-300 tracking-[0.2em]">Kembalian Petugas</span>
                                    <span id="change_amount" class="text-5xl font-black leading-none mt-2">Rp 0</span>
                                </div>

                                <button id="pay_exit_btn" type="submit" class="w-full bg-white text-indigo-600 hover:bg-indigo-50 font-black py-6 px-4 rounded-[2rem] text-2xl shadow-xl transition-all active:scale-95 flex items-center justify-center gap-3">
                                    BUKA GATE
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </button>
                            </form>
                        @else
                            <div class="text-center py-10">
                                <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h4 class="text-3xl font-black mb-10">TRANSAKSI SELESAI</h4>
                                <a href="{{ route('attendant.transaction.index') }}" class="w-full inline-block bg-white text-indigo-600 font-black py-5 px-8 rounded-[2rem] text-xl shadow-xl">
                                    Dashboard Utama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalAmount = {{ $paymentInfo['amount'] ?? 0 }};
            const paidInput = document.getElementById('paid_amount');
            const changeCard = document.getElementById('change_card');
            const changeAmount = document.getElementById('change_amount');

            if (!paidInput) return;

            function updateChange() {
                const paidValue = parseInt(paidInput.value, 10) || 0;
                const change = paidValue - totalAmount;

                if (change > 0) {
                    changeCard.classList.remove('hidden');
                    changeAmount.textContent = 'Rp ' + change.toLocaleString('id-ID');
                } else {
                    changeCard.classList.add('hidden');
                }
            }

            paidInput.addEventListener('input', updateChange);
            updateChange();
        });
    </script>
</x-app-layout>