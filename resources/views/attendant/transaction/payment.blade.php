<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Proses Pembayaran Parkir') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                
                <div class="bg-blue-600 p-6 text-center text-white">
                    <p class="text-blue-100 text-sm uppercase tracking-widest font-bold mb-1">Billing Summary</p>
                    <h2 class="text-3xl font-black italic font-mono">{{ $transaction->plate_number }}</h2>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-tight">Warna Kendaraan</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $transaction->vehicle_color }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-tight">Area Parkir</p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $transaction->area->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-tight">Jenis</p>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-bold text-gray-600 dark:text-gray-300">
                                {{ ucfirst($transaction->rate->vehicle_type) }}
                            </span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-tight">Waktu Masuk</p>
                            <p class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $transaction->entry_time->format('d M, H:i') }}</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-100 dark:border-blue-800 mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-xs font-bold text-blue-500 uppercase italic">Durasi Terhitung</p>
                                <p class="text-2xl font-black text-blue-700 dark:text-blue-400">
                                    {{ $duration['hours'] }}j {{ $duration['duration_minutes'] % 60 }}m
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-blue-500 uppercase italic">Tarif/Jam</p>
                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($transaction->rate->amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-blue-200 dark:border-blue-800 flex justify-between items-end">
                            <p class="text-sm font-bold text-gray-500 uppercase">Total Tagihan</p>
                            <p class="text-4xl font-black text-emerald-600 dark:text-emerald-400">
                                <span class="text-xl">Rp</span>{{ number_format($duration['amount'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('attendant.transaction.exit.process', $transaction->id) }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="space-y-3">
                            <label for="paid_amount" class="block text-sm font-black text-gray-700 dark:text-gray-300 uppercase">
                                Bayar Tunai (Cash)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 text-lg">Rp</span>
                                <input 
                                    type="number" 
                                    id="paid_amount" 
                                    name="paid_amount" 
                                    required 
                                    min="{{ $duration['amount'] }}" 
                                    step="500"
                                    value="{{ $duration['amount'] }}"
                                    class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-900 rounded-xl text-2xl font-black focus:border-blue-500 focus:ring-0 transition-all text-emerald-600"
                                    onfocus="this.select()"
                                >
                            </div>
                            @error('paid_amount')
                                <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="changeContainer" class="hidden transform transition-all duration-300">
                            <div class="p-4 bg-amber-50 dark:bg-amber-900/30 border-2 border-dashed border-amber-300 dark:border-amber-800 rounded-xl flex justify-between items-center">
                                <p class="text-sm font-bold text-amber-700 dark:text-amber-400 uppercase tracking-tighter">Uang Kembalian</p>
                                <p class="text-2xl font-black text-amber-600 dark:text-amber-400">Rp <span id="changeAmount">0</span></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4">
                            <a href="{{ route('attendant.transaction.index') }}" 
                               class="px-6 py-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 text-center font-bold transition-colors">
                                BATAL
                            </a>
                            <button type="submit" 
                                    class="px-6 py-4 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-black shadow-lg shadow-emerald-200 dark:shadow-none transition-all hover:-translate-y-1">
                                SELESAI & BAYAR
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 flex items-center justify-center gap-2 opacity-40">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Secure Transaction System</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const minAmount = {{ $duration['amount'] }};
        const paidAmountInput = document.getElementById('paid_amount');
        const changeContainer = document.getElementById('changeContainer');
        const changeAmount = document.getElementById('changeAmount');

        function calculateChange() {
            const paid = parseFloat(paidAmountInput.value) || 0;
            const change = paid - minAmount;

            if (change > 0) {
                changeContainer.classList.remove('hidden');
                changeContainer.classList.add('block');
                changeAmount.textContent = new Intl.NumberFormat('id-ID').format(change);
            } else {
                changeContainer.classList.add('hidden');
                changeContainer.classList.remove('block');
            }
        }

        paidAmountInput.addEventListener('input', calculateChange);
        
        // Initial call
        window.addEventListener('DOMContentLoaded', calculateChange);
    </script>
</x-app-layout>