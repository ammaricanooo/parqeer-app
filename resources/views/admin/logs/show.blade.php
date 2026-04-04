<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Detail Audit Log') }}
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('admin.logs.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Log
                </a>
            </div>

            <div class="bg-white  shadow-xl shadow-indigo-500/5 rounded-[2rem] overflow-hidden border border-gray-100 ">
                <div class="px-8 py-10 bg-gradient-to-br from-indigo-50 to-transparent border-b border-gray-100 ">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="px-3 py-1 bg-indigo-100  text-indigo-700  text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                                Activity ID #{{ $logActivity->id }}
                            </span>
                            <h3 class="text-4xl font-black text-gray-900  mt-3 tracking-tighter">
                                {{ strtoupper($logActivity->activity) }}
                            </h3>
                        </div>
                        <div class="text-left md:text-right">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Kejadian</p>
                            <p class="text-lg font-black text-gray-700 ">{{ $logActivity->created_at->format('d M Y') }}</p>
                            <p class="text-3xl font-black text-indigo-600 leading-none">{{ $logActivity->created_at->format('H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="mb-10">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 border-b border-gray-100  pb-2">Informasi Kendaraan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg">
                                    {{ substr($logActivity->plate_number, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Plat</p>
                                    <p class="text-2xl font-black text-gray-900  leading-none">{{ $logActivity->plate_number }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gray-100  rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.172-1.172a4 4 0 115.656 5.656l-1.172 1.172"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Warna Kendaraan</p>
                                    <p class="text-xl font-bold text-gray-700 ">{{ $logActivity->vehicle_color }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 pb-2 border-b border-gray-100 ">Operator</h4>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($logActivity->user?->name ?? 'S', 0, 1) }}
                                    </div>
                                    <p class="font-black text-gray-900  uppercase tracking-tight">
                                        {{ $logActivity->user?->name ?? 'SYSTEM AUTO' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 pb-2 border-b border-gray-100 ">Detail Aktivitas</h4>
                            <div class="p-4 bg-gray-50 /50 rounded-2xl border border-gray-100 ">
                                <p class="text-sm font-bold text-gray-600  leading-relaxed italic italic">
                                    "{{ $logActivity->description }}"
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-6 border-t border-gray-100  flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">
                        <span>Parqeer Audit Trail System</span>
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Verified Entry
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>