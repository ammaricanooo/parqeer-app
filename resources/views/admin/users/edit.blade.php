<x-app-layout>
    <x-slot name="title">
        Edit Data Pengguna
    </x-slot>

    <section class="py-6">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="bg-white relative shadow-md rounded-lg space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1 flex items-center px-4 py-2">
                        <h5>
                            <span class="text-gray-800 font-semibold">Edit Data Pengguna</span>
                        </h5>
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t">
                </div>
                <div class="">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}"
                        enctype="multipart/form-data">
                        @method('PUT')
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
                                    placeholder="Masukkan Nama Lengkap" required="" value="{{ old('name', $user->name) }}">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <label for="username"
                                    class="block mb-2 text-sm font-medium text-gray-900">Nama Pengguna</label>
                                <input type="text" name="username" id="username"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    placeholder="Masukkan Nama Pengguna" required=""
                                    value="{{ old('username', $user->username) }}">
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>
                            <div>
                                <label for="password"
                                    class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                                <input type="text" name="password" id="password"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    placeholder="Masukkan Password" value="{{ old('password') }}">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                                <select id="role" name="role"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    required>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Admin</option>
                                    <option value="attendant"
                                        {{ old('role', $user->role) == 'attendant' ? 'selected' : '' }}>Petugas</option>
                                    <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>
                                        Owner</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div>
                                <label for="status"
                                    class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                                <select id="status" name="status"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                    required>
                                    <option value="active"
                                        {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive"
                                        {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                        </div>
                        <div class="flex items-center justify-end gap-4 w-full mt-4">
                        <button type="submit"
                            class="flex items-center justify-center text-white bg-primary hover:bg-primary/75 focus:ring-4 focus:ring-primary/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                class="h-5 w-5 mr-2 -ml-0.5">
                                <path fill="currentColor"
                                    d="M21 7v12q0 .825-.587 1.413T19 21H5q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h12zm-2 .85L16.15 5H5v14h14zM12 18q1.25 0 2.125-.875T15 15t-.875-2.125T12 12t-2.125.875T9 15t.875 2.125T12 18m-6-8h9V6H6zM5 7.85V19V5z" />
                            </svg>
                            Simpan Data Pengguna
                        </button>
                        <a href="/admin/users"
                            class="flex items-center justify-center text-white bg-gray-400 hover:bg-gray-400/75 focus:ring-4 focus:ring-gray-400/20 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                            Kembali
                        </a>
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
