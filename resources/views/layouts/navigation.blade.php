<nav x-data="{ open: false }"
    x-effect="open ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')"
    class="bg-white  border-b border-gray-100 ">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 lg:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img class="w-20" src="/img/logo.png" alt="diskominfo" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    @if (Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                            {{ __('Pengguna') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.rates.index')" :active="request()->routeIs('admin.rates.index')">
                            {{ __('Tarif Parkir') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.areas.index')" :active="request()->routeIs('admin.areas.index')">
                            {{ __('Area Parkir') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.vehicles.index')" :active="request()->routeIs('admin.vehicles.index')">
                            {{ __('Vehicles') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.index')">
                            {{ __('Logs Aktivitas') }}
                        </x-nav-link>
                    @endif
                    @if (Auth::user()->role === 'attendant')
                        <x-nav-link :href="route('attendant.transaction.index')" :active="request()->routeIs('attendant.transaction.index')">
                            {{ __('Transactions') }}
                        </x-nav-link>
                    @endif
                    @if (Auth::user()->role === 'owner')
                        <x-nav-link :href="route('owner.dashboard')" :active="request()->routeIs('owner.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:ms-6">
                <div id="photo-preview-container" class="w-10 h-10 rounded-full overflow-hidden">
                    @if (Auth::user()->photo)
                        <img id="photo-preview" src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}"
                            class="w-full h-full object-cover">
                    @else
                        <div id="photo-placeholder" class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112 15.996c4.125 0 7.81 1.755 10.354 4.555z" />
                                <path d="M16.5 5.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500  bg-white  hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} ({{ Auth::user()->role }})</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); showLogoutConfirmation();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400  hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }"
        class="hidden lg:hidden fixed w-full bg-white/75 z-40 backdrop-blur-lg h-screen">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                    {{ __('Pengguna') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.rates.index')" :active="request()->routeIs('admin.rates.index')">
                    {{ __('Tarif Parkir') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.areas.index')" :active="request()->routeIs('admin.areas.index')">
                    {{ __('Area Parkir') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.vehicles.index')" :active="request()->routeIs('admin.vehicles.index')">
                    {{ __('Vehicles') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.logs.index')" :active="request()->routeIs('admin.logs.index')">
                    {{ __('Logs Aktivitas') }}
                </x-responsive-nav-link>
            @endif
            @if (Auth::user()->role === 'attendant')
                <x-responsive-nav-link :href="route('attendant.transaction.index')" :active="request()->routeIs('attendant.transaction.index')">
                    {{ __('Transactions') }}
                </x-responsive-nav-link>
            @endif
            @if (Auth::user()->role === 'owner')
                <x-responsive-nav-link :href="route('owner.dashboard')" :active="request()->routeIs('owner.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white ">
            <div class="px-4 flex gap-4 items-center">
                <div id="photo-preview-container" class="w-10 h-10 rounded-full overflow-hidden">
                    @if (Auth::user()->photo)
                        <img id="photo-preview" src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}"
                            class="w-full h-full object-cover">
                    @else
                        <div id="photo-placeholder" class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112 15.996c4.125 0 7.81 1.755 10.354 4.555z" />
                                <path d="M16.5 5.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
                    <div>
                        <div class="font-medium text-base text-gray-800 ">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->role }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
</nav>

<script>
    function showLogoutConfirmation() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan keluar dari akun ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#242F9B',
            confirmButtonText: 'Ya, logout!',
            cancelButtonText: 'Tidak, tetap di sini!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengkonfirmasi, submit form logout
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
