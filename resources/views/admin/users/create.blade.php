<x-app-layout>
    <x-slot name="title">
        Tambah Data Pengguna Parkir
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Pengguna Parkir') }}
        </h2>
    </x-slot>


    <section class="pt-12 pb-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white relative shadow-md sm:rounded-lg space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1 flex items-center px-4 py-2">
                        <h5>
                            <span class="text-gray-800 font-semibold">Tambah Data Pengguna</span>
                        </h5>
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">
                </div>
                <div class="">
                    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="grid mb-4 sm:col-span-2 md:mb-6 sm:grid-cols-4">
                            <div>
                                <label for="photo" class="block mb-2 text-sm font-medium text-gray-900">Foto</label>
                                <img id="photo-preview" class="object-cover object-center rounded-lg mb-2 max-w-40">
                                <input type="file" name="photo" id="photo"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    placeholder="Masukkan Foto" value="{{ old('photo') }}" onchange="pereviewPhoto()">
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid gap-4 sm:col-span-2 md:gap-6 sm:grid-cols-4">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap</label>
                                <input type="text" name="name" id="name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    placeholder="Masukkan Nama Lengkap" required="" value="{{ old('name') }}">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <label for="username"
                                    class="block mb-2 text-sm font-medium text-gray-900">Nama Pengguna</label>
                                <input type="text" name="username" id="username"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    placeholder="Masukkan Nama Pengguna" required="" value="{{ old('username') }}">
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>
                            <div>
                                <label for="password"
                                    class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                                <input type="text" name="password" id="password"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    placeholder="Masukkan Password" required="" value="{{ old('password') }}">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <label for="role"
                                    class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                                <select id="role" name="role"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    required>
                                        <option value="admin">Admin</option>
                                        <option value="attendant">Petugas</option>
                                        <option value="owner">Owner</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>
                            <div>
                                <label for="status"
                                    class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                                <select id="status" name="status"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    required>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Tidak Aktif</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>
                        <div class="block w-full mt-4">
                            <button type="submit"
                                class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                <svg class="h-3.5 w-3.5 mr-1.5 -ml-1" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                </svg>
                                Tambah Data Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function pereviewPhoto() {
            const photo = document.querySelector('#photo');
            const imgPreview = document.querySelector('#photo-preview');

            imgPreview.style.display = 'block';

            const oFReader = new FileReader();
            oFReader.readAsDataURL(photo.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            };
        }
    </script>
</x-app-layout>