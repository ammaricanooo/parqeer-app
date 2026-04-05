<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Parqeer App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body x-data="{ page: 'signin', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" 
      x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')); $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" 
      :class="{ 'dark text-bodydark bg-slate-900': darkMode === true }" class="bg-gray-50 text-slate-800">

    <include src="./partials/preloader.html"></include>

    <div class="flex h-screen overflow-hidden">
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            
            <main class="min-h-screen flex items-center justify-center p-4 md:p-6 my-8">
                <div class="w-full max-w-5xl">
                    <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark">
                        <div class="flex flex-wrap items-stretch">
                            
                            <div class="hidden w-full lg:block lg:w-1/2 bg-primary/[0.03] dark:bg-white/[0.02]">
                                <div class="px-12 py-10 text-center h-full flex flex-col justify-center items-center">
                                    <a class="mb-8 flex items-center gap-3" href="/">
                                        <img class="w-12" src="/img/logo.png" alt="logo" />
                                        <span class="text-2xl font-bold text-black dark:text-white">Parqeer</span>
                                    </a>

                                    <p class="font-medium text-slate-500 dark:text-slate-400 max-w-xs mx-auto leading-relaxed">
                                        "Solusi parkir pintar untuk kemudahan dan kenyamanan Anda."
                                    </p>

                                    <div class="mt-10">
                                        <img src="/img/illustration-03.svg" alt="illustration" class="max-w-full h-auto transform hover:scale-105 transition duration-500" />
                                    </div>
                                </div>
                            </div>

                            <div class="w-full lg:w-1/2 lg:border-l border-stroke dark:border-strokedark">
                                <div class="w-full p-8 sm:p-12.5 xl:p-17.5">
                                    
                                    <div class="lg:hidden mb-8 flex flex-col items-center">
                                        <img class="w-16 mb-4" src="/img/logo.png" alt="logo" />
                                        <h1 class="text-2xl font-bold text-black dark:text-white">Parqeer App</h1>
                                    </div>

                                    <h2 class="mb-2 text-2xl font-bold text-black dark:text-white sm:text-title-xl2">
                                        Selamat Datang Kembali
                                    </h2>
                                    <p class="mb-8 text-sm text-slate-500 dark:text-slate-400">Silakan masuk ke akun Anda</p>

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        
                                        <div class="mb-5">
                                            <label for="username" class="mb-2 block font-medium text-black dark:text-white">Username</label>
                                            <div class="relative">
                                                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                                                    placeholder="Masukkan username"
                                                    class="w-full rounded-xl border border-slate-200 bg-transparent py-4 pl-6 pr-12 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 dark:border-slate-700 dark:bg-slate-800" />
                                                
                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                                    <svg class="fill-current" width="22" height="22" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                                </span>
                                            </div>
                                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                                        </div>

                                        <div class="mb-5">
                                            <div class="relative">
                                                <input id="password" type="password" name="password" required
                                                    placeholder="••••••••"
                                                    class="w-full rounded-xl border border-slate-200 bg-transparent py-4 pl-6 pr-12 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 dark:border-slate-700 dark:bg-slate-800" />
                                                
                                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                                    <svg class="fill-current" width="22" height="22" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/></svg>
                                                </span>
                                            </div>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <div class="mb-6 flex items-center">
                                            <label for="remember_me" class="flex cursor-pointer items-center text-sm font-medium text-slate-600 dark:text-slate-400">
                                                <input id="remember_me" type="checkbox" name="remember" class="mr-3 h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary/30" />
                                                Ingat saya di perangkat ini
                                            </label>
                                        </div>

                                        <button type="submit" class="w-full rounded-xl bg-primary p-4 font-semibold text-white shadow-lg shadow-primary/30 transition hover:bg-opacity-90 active:scale-[0.98]">
                                            Sign In
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 text-center">
                        <p class="text-xs font-medium uppercase tracking-widest text-slate-400 dark:text-slate-500">
                            © 2026 Ammar Abdul Malik <span class="mx-2">•</span> XII RPL <br>
                            <span class="text-[10px]">SMK BINA INFORMATIKA</span>
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>