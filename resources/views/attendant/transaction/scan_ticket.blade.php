<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Scan Gate Parkir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden p-6">
                <h3 class="text-lg font-bold mb-4">Transaksi #{{ $transaction->id }}</h3>

                @if(isset($message))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        {{ $message }}
                    </div>
                @endif

                @if(isset($error))
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                        {{ $error }}
                    </div>
                @endif

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Status</span><span class="font-bold uppercase">{{ $transaction->status }}</span></div>
                    <div class="flex justify-between"><span>Plat</span><span class="font-bold">{{ $transaction->plate_number }}</span></div>
                    <div class="flex justify-between"><span>Area</span><span class="font-bold">{{ $transaction->area->name }}</span></div>
                    <div class="flex justify-between"><span>Warna</span><span class="font-bold">{{ $transaction->vehicle_color }}</span></div>
                    <div class="flex justify-between"><span>Entry</span><span class="font-bold">{{ $transaction->entry_time->format('d/m/Y H:i') }}</span></div>
                </div>

                <div class="mt-6">
                    @if($transaction->status === 'in')
                        <a href="{{ route('payment.public', $transaction->id) }}" class="w-full inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Bayar Sekarang
                        </a>
                    @else
                        <a href="{{ route('attendant.transaction.index') }}" class="w-full inline-block text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali ke Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>