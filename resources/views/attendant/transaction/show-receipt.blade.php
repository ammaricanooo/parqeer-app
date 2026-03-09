<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Struk & Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Struk Preview -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Struk Keluar</h3>
                        
                        <!-- Struk Content -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg font-mono text-sm border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <!-- Header -->
                            <div class="text-center mb-4 border-b border-gray-300 pb-4">
                                <h2 class="text-2xl font-black">PARQEER</h2>
                                <p class="text-xs uppercase tracking-widest text-gray-600">Bukti Keluar Parkir</p>
                                <p class="text-xs text-gray-600 mt-1">ID: #{{ $transaction->id }}</p>
                            </div>

                            <!-- Plate -->
                            <div class="text-center mb-4 bg-gray-100 dark:bg-gray-800 border-2 border-black px-3 py-3 rounded">
                                <p class="text-xs uppercase text-gray-600 mb-1">Nomor Kendaraan</p>
                                <p class="text-3xl font-black tracking-wider">{{ $transaction->plate_number }}</p>
                            </div>

                            <!-- Details -->
                            <div class="space-y-2 mb-4 text-xs border-b border-dashed border-gray-300 pb-4">
                                <div class="flex justify-between">
                                    <span class="uppercase text-gray-600">Area</span>
                                    <span class="font-bold">{{ $transaction->area->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="uppercase text-gray-600">Masuk</span>
                                    <span class="font-bold">{{ $transaction->entry_time->format('d/m/y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="uppercase text-gray-600">Keluar</span>
                                    <span class="font-bold">{{ $transaction->exit_time->format('d/m/y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="uppercase text-gray-600">Durasi</span>
                                    <span class="font-bold">{{ $transaction->duration_minutes }} menit</span>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="text-center bg-gray-100 dark:bg-gray-800 p-3 rounded font-black mb-4">
                                <p class="text-xs uppercase text-gray-600 mb-1">Total Biaya</p>
                                <p class="text-2xl">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                            </div>

                            <!-- Status -->
                            @if($transaction->status === 'paid')
                                <div class="text-center bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 p-2 rounded font-bold text-xs uppercase">
                                    ✓ LUNAS
                                </div>
                            @else
                                <div class="text-center bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-100 p-2 rounded font-bold text-xs">
                                    ⚠ Menunggu Pembayaran
                                </div>
                            @endif

                            <!-- Footer -->
                            <div class="text-center text-xs text-gray-600 mt-4 pt-4 border-t border-gray-300">
                                <p>Terima kasih</p>
                                <p>{{ now()->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-6">
                            <a href="{{ route('attendant.transaction.receipt-pdf', $transaction->id) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                📥 Download PDF
                            </a>
                            <a href="{{ route('attendant.transaction.index') }}" 
                               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-center">
                                ← Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div>
                    @if($transaction->status !== 'paid')
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 sticky top-6">
                            <h3 class="text-lg font-semibold mb-4">Pembayaran</h3>
                            
                            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Biaya</p>
                                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                            </div>

                            <form action="{{ route('attendant.transaction.pay', $transaction->id) }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="paid_amount" class="block text-sm font-medium mb-1">
                                        Jumlah Bayar (Rp)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="paid_amount" 
                                        name="paid_amount"
                                        min="{{ $transaction->amount }}"
                                        step="1000"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 focus:outline-none focus:ring-blue-500"
                                        value="{{ old('paid_amount', $transaction->amount) }}"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">Min. Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                </div>

                                <div id="change-info" class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded hidden">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Kembalian</p>
                                    <p id="change-amount" class="text-lg font-bold">Rp 0</p>
                                </div>

                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    ✓ Proses Pembayaran
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4 text-green-600">Pembayaran Selesai</h3>
                            
                            <div class="space-y-3">
                                <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Biaya</p>
                                    <p class="text-xl font-bold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                </div>

                                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Dibayar</p>
                                    <p class="text-xl font-bold">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</p>
                                </div>

                                <div class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Kembalian</p>
                                    <p class="text-xl font-bold">Rp {{ number_format($transaction->change, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <a href="{{ route('attendant.transaction.payment-receipt', $transaction->id) }}"
                               class="w-full block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                📥 Download Struk Pembayaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paidAmountInput = document.getElementById('paid_amount');
            const changeInfo = document.getElementById('change-info');
            const changeAmount = document.getElementById('change-amount');
            const totalBiaya = {{ $transaction->amount }};

            const updateChange = () => {
                const paidAmount = parseFloat(paidAmountInput.value) || 0;
                if (paidAmount >= totalBiaya) {
                    const change = paidAmount - totalBiaya;
                    changeAmount.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
                    changeInfo.classList.remove('hidden');
                } else {
                    changeInfo.classList.add('hidden');
                }
            };

            paidAmountInput.addEventListener('input', updateChange);
            updateChange();
        });
    </script>
</x-app-layout>
