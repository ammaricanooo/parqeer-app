<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Parqeer') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100  pb-4">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white  shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @if (session()->has('success'))
            <script>
                Swal.fire({
                    type: "success",
                    icon: "success",
                    title: "BERHASIL!",
                    text: "{{ session('success') }}",
                    timer: 1500,
                    showConfirmButton: false,
                    showCancelButton: false,
                    buttons: false,
                });
            </script>
        @elseif (session()->has('error'))
            <script>
                Swal.fire({
                    type: "error",
                    icon: "error",
                    title: "GAGAL!",
                    text: "{{ session('error') }}",
                    timer: 1500,
                    showConfirmButton: false,
                    showCancelButton: false,
                    buttons: false,
                });
            </script>
        @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
                <p class="text-center text-xs text-gray-400">© 2026 Ammar Abdul Malik - XII RPL <br>SMK BINA INFORMATIKA</p>
            </main>
        </div>
        <script src="https://website-widgets.pages.dev/dist/sienna.min.js" defer></script> 
    </body>
</html>
