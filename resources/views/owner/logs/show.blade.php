<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Waktu</p>
                        <p class="font-bold">{{ $logActivity->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Aktivitas</p>
                            <p class="font-bold">{{ ucfirst($logActivity->activity) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Plat</p>
                            <p class="font-bold">{{ $logActivity->plate_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Warna</p>
                            <p class="font-bold">{{ $logActivity->vehicle_color }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Operator</p>
                            <p class="font-bold">{{ $logActivity->user?->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Deskripsi</p>
                        <p class="font-medium">{{ $logActivity->description }}</p>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('owner.logs.index') }}" class="text-blue-600">&larr; Kembali ke daftar</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
