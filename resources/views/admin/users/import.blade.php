<x-app-layout>
    <x-slot name="title">
        Import Data User
    </x-slot>

    <section class="pt-12 pb-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold mb-4">Import Data User (Excel)</h3>

                <form action="{{ route('admin.users.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium mb-1">Pilih file (xlsx, xls, csv)</label>
                        <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-xs mt-2">Format: header harus sesuai template export: <strong>Foto, Nama, Username, Role, Status</strong>. Kolom <em>Foto</em> diabaikan saat import. Default password: <strong>admin1234#</strong></p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.import.template') }}" class="bg-[#107c41] text-white px-4 py-2 rounded">Download Template (Layout Export)</a>
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Import</button>
                        <a href="{{ route('admin.users') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </section>
</x-app-layout>
