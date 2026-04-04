<x-app-layout>
    <x-slot name="title">
        Log Aktivitas
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4">
                    <h5 class="text-gray-800 font-semibold">Log Aktivitas</h5>
                </div>

                <!-- Filter & Search -->
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">

                    <div class="w-full">
                        <form method="GET" class="flex flex-col md:flex-row gap-3">
                            <!-- Search -->
                            <div class="relative w-full md:w-1/2">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Cari plat, deskripsi, atau operator"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 focus:border-primary block w-full pl-10 p-2">
                            </div>

                            <!-- Activity Filter -->
                            <select
                                name="activity"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 focus:border-primary p-2 w-40">
                                <option value="">Semua Aktivitas</option>
                                <option value="entry" {{ request('activity') == 'entry' ? 'selected' : '' }}>
                                    Entry (Masuk)
                                </option>
                                <option value="exit" {{ request('activity') == 'exit' ? 'selected' : '' }}>
                                    Exit (Keluar)
                                </option>
                                <option value="payment" {{ request('activity') == 'payment' ? 'selected' : '' }}>
                                    Payment (Bayar)
                                </option>
                            </select>

                            <!-- Button -->
                            <button
                                type="submit"
                                class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 border border-primary focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-5 py-2 focus:outline-none">
                                Filter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50">
                            <tr>
                                <th class="p-4">Waktu</th>
                                <th class="p-4">Aktivitas</th>
                                <th class="p-4">Plat</th>
                                <th class="p-4">Warna</th>
                                <th class="p-4">Operator</th>
                                <th class="p-4">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr
                                    class="border-b hover:bg-gray-100 cursor-pointer"
                                    onclick="window.location='{{ route('admin.logs.show', $log->id) }}'">
                                    
                                    <td class="px-4 py-3 text-sm">
                                        {{ $log->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $log->activity === 'entry'
                                                ? 'bg-blue-100 text-blue-800'
                                                : ($log->activity === 'exit'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($log->activity) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 font-mono font-semibold">
                                        {{ $log->plate_number }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $log->vehicle_color }}
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        {{ $log->user?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        {{ \Illuminate\Support\Str::limit($log->description, 80) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        Tidak ada log aktivitas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>
    </section>
</x-app-layout>
