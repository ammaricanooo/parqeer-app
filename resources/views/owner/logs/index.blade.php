<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" class="mb-4">
                        <div class="flex gap-2">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari plat, deskripsi, atau operator" class="w-2/3 px-3 py-2 border rounded" />
                            <select name="activity" class="px-3 py-2 border rounded">
                                <option value="">Semua Aktivitas</option>
                                <option value="entry" {{ request('activity') == 'entry' ? 'selected' : '' }}>Entry</option>
                                <option value="exit" {{ request('activity') == 'exit' ? 'selected' : '' }}>Exit</option>
                                <option value="payment" {{ request('activity') == 'payment' ? 'selected' : '' }}>Payment</option>
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="text-left">
                                    <th class="px-4 py-2">Waktu</th>
                                    <th class="px-4 py-2">Aktivitas</th>
                                    <th class="px-4 py-2">Plat</th>
                                    <th class="px-4 py-2">Warna</th>
                                    <th class="px-4 py-2">Operator</th>
                                    <th class="px-4 py-2">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2">{{ ucfirst($log->activity) }}</td>
                                        <td class="px-4 py-2"><a href="{{ route('owner.logs.show', $log->id) }}" class="text-blue-600">{{ $log->plate_number }}</a></td>
                                        <td class="px-4 py-2">{{ $log->vehicle_color }}</td>
                                        <td class="px-4 py-2">{{ $log->user?->name ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ Str::limit($log->description, 100) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-600">Tidak ada log.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
