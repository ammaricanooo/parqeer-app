<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 ">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 ">
            {{ __("Update your account's profile information.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Photo Upload -->
        <div>
            <x-input-label for="photo" :value="__('Profile Photo')" />
            <div class="mt-2 flex items-center gap-4">
                <div id="photo-preview-container" class="w-20 h-20 rounded-full overflow-hidden">
                    @if ($user->photo)
                        <img id="photo-preview" src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div id="photo-placeholder" class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112 15.996c4.125 0 7.81 1.755 10.354 4.555z" />
                                <path d="M16.5 5.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <input id="photo" name="photo" type="file" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewPhoto()" />
                    <p class="mt-1 text-xs text-gray-600">PNG, JPG, GIF max 2MB</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 "
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
    function previewPhoto() {
        const photoInput = document.querySelector('#photo');
        const previewContainer = document.querySelector('#photo-preview-container');

        if (photoInput.files && photoInput.files[0]) {
            const file = photoInput.files[0];

            // Validate file type
            if (!file.type.match('image.*')) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                // Replace placeholder with image preview
                previewContainer.innerHTML = `
                    <img id="photo-preview" src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">
                `;
            };
            reader.readAsDataURL(file);
        }
    }
</script>