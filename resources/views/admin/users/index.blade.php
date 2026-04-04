<x-app-layout>
    <x-slot name="title">
        Data Pengguna Parqeer
    </x-slot>

    <!-- Start block -->
    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg overflow-hidden">
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="flex-1 flex items-center space-x-2">
                        <h5>
                            <span class="text-gray-800 font-semibold">Data Pengguna Parqeer</span>
                        </h5>
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" action="/admin/users">
                            <label for="simple-search" class="sr-only">Cari</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="currentColor"
                                        viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" placeholder="Cari data pengguna"
                                    value="{{ request('search') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-0 focus:border-primary block w-full pl-10 p-2">
                            </div>
                            <button type="submit"
                                class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 border border-primary focus:ring-4 focus:ring-primary/20 font-medium rounded-r-lg text-sm px-4 py-2 focus:outline-none">
                                Cari
                            </button>
                        </form>
                    </div>
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <a href="{{ route('admin.users.export') }}"
                            class="flex items-center justify-center text-white bg-[#107c41] hover:bg-[#107c41]/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="4">
                                    <path stroke-linejoin="round"
                                        d="M8 15V6a2 2 0 0 1 2-2h28a2 2 0 0 1 2 2v36a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-9" />
                                    <path d="M31 15h3m-6 8h6m-6 8h6" />
                                    <path stroke-linejoin="round" d="M4 15h18v18H4zm6 6l6 6m0-6l-6 6" />
                                </g>
                            </svg>
                            Export Data Pengguna (Excel)
                        </a>
                        <a href="{{ route('admin.users.create') }}"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Tambah Data Pengguna
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="p-4">Foto</th>
                                <th scope="col" class="p-4">Nama Lengkap</th>
                                <th scope="col" class="p-4">Nama Pengguna</th>
                                <th scope="col" class="p-4">Role</th>
                                <th scope="col" class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b hover:bg-gray-100">
                                    <td scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-center mr-3 min-w-16 max-w-16">
                                            @if ($user->photo)
                                                <img src="{{ asset('storage/users/' . $user->photo) }}"
                                                    alt="{{ $user->name }}">
                                            @else
                                                <img src="https://telegra.ph/file/24fa902ead26340f3df2c.png"
                                                    alt="{{ $user->name }}">
                                            @endif

                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-center mr-3">{{ $user->name }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-centers max-w-sm lg:max-w-2xl">
                                            <p class="text-ellipsis overflow-hidden">{{ $user->username }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-centers max-w-sm lg:max-w-2xl">
                                            <p class="text-ellipsis overflow-hidden">
                                                {{ match ($user->role) {
                                                    'admin' => 'Admin',
                                                    'attendant' => 'Petugas',
                                                    'owner' => 'Owner',
                                                } }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-centers max-w-sm lg:max-w-2xl">
                                            <p class="text-ellipsis overflow-hidden">
                                                {{ $user->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-center space-x-4">
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="py-2 px-3 flex items-center text-sm font-medium text-center text-white bg-primary rounded-lg hover:bg-primary/75 focus:ring-4 focus:outline-none focus:ring-primary/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-0.5"
                                                    viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path
                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form id="user-destroy-{{ $user->id }}"
                                                action="{{ route('admin.users.destroy', $user->id) }}" method="post">
                                                @method('delete')
                                                @csrf
                                                <button
                                                    class="flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center"
                                                    onclick="event.preventDefault(); confirmDelete({{ $user->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4 mr-2 -ml-0.5" viewbox="0 0 20 20"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav class="space-y-3 md:space-y-0 p-4">
                    {{ $users->links() }}
                </nav>
            </div>
        </div>
    </section>
    <!-- End block -->
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Untuk menghapus data sejarah ini?!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('user-destroy-' + id).submit();
                }
            })
        }
    </script>
</x-app-layout>
