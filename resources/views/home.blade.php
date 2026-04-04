<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parqeer App</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- CSS styles --}}
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/custom.css">

    {{-- Javascript $ Libraries --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/js/jquery.webticker.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="/js/script.js"></script>

    {{-- ION Icons --}}
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="font-sans bg-base text-gray-900">

    {{-- Preloader Start --}}
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    {{-- Preloader End --}}

    {{-- Cursor Start --}}
    <div class="cursor"></div>
    <div class="cursor2"></div>
    {{-- Cursor End --}}

    {{-- Scroll To Top $ Scroll Progress Start --}}
    <button class="scroll-top-btn">
        <svg class="progress-ring" width="60" height="60">
            <circle class="progress-ring__circle" r="27" cx="30" cy="30" />
        </svg>
    </button>
    {{-- Scroll To Top $ Scroll Progress End --}}

    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
        $now = Carbon::now();
    @endphp
    {{-- <div id="navbar-top"
        class="text-white py-5 px-5 fixed top-0 left-0 w-full z-50 transition-all duration-500 ease-in-out">
        <div class="w-full h-full mx-auto px-5 max-w-screen-xl flex items-center">

            <p class="">{{ $now->translatedFormat('l, j F Y') }}</p>
        </div>
    </div> --}}

    {{-- Header Section Start --}}
    <header id="header"
        class="fixed top-0 w-full h-auto z-50 font-semibold text-white transition-all duration-500 ease-in-out py-12">
        <div class="w-full h-full mx-auto px-5 max-w-screen-xl">
            <div class="relative h-full w-full flex justify-between items-center">
                <div class="flex gap-1 font-semibold">
                    <a href="/" class="flex items-center gap-2 text-current">
                        <img src="/img/logo.png" alt="diskominfo" class="w-20 h-10 object-cover">
                        <div class="flex flex-col gap-0">
                            <p class="uppercase font-bold">PARQEER</p>
                            <p class="text-sm">Internal Management System</p>
                        </div>
                    </a>
                </div>
                <div class="flex items-center">
                    <nav id="nav" class="hidden xl:flex items-center justify-center text-base gap-4 text-white">
                        {{-- Check Authorization Start --}}
                        @if (Auth::check() && Auth::user())
                            <a href="{{ route('dashboard') }}"
                                class="h-8 rounded-full px-3 text-current flex items-center justify-center nav-link">Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="h-8 rounded-full px-3 text-current flex items-center justify-center nav-link">Login
                            </a>
                        @endif
                        {{-- Google Translate Start --}}
                        <div>
                            {{-- Google Translate Trigger Button (gambar Google Translate) --}}
                            <button id="translateButton" class="border-none bg-none mt-2 p-0">
                                <img src="https://www.google.com/images/icons/product/translate-32.png"
                                    alt="Google Translate" style="cursor: pointer;">
                            </button>
                            {{-- Google Translate Widget --}}
                            <div class="relative">
                                <div id="google_translate_element"
                                    class="hidden absolute z-10 text-center -bottom-28 -left-72">
                                </div>
                            </div>
                        </div>
                        {{-- Google Translate End --}}
                    </nav>
                </div>
                {{-- Button Drawer For Mobile --}}
                <div class="buttons absolute right-0 top-1/2 -translate-y-1/2 flex gap-1">
                    <button id="header-drawer-button" aria-label="Toggle drawer open and closed"
                        class="flex xl:hidden size-9 rounded-full p-2 items-center justify-center bg-transparent hover:bg-white/20 stroke-current hover:stroke-white ">
                        <svg id="drawer-open" class="size-full stroke-current">
                            <use href="/svg/ui.svg#menu"></use>
                        </svg>
                        <svg id="drawer-close" class="size-full stroke-current">
                            <use href="/svg/ui.svg#x"></use>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
    {{-- Drawer For Mobile --}}
    <div id="drawer"
        class="fixed inset-0 h-0 z-40 overflow-hidden flex flex-col items-center justify-center lg:hidden bg-base/80 transition-[height] duration-300 ease-in-out backdrop-blur-lg">
        <nav class="flex flex-col items-center space-y-2 text-dark font-semibold gap-4">
            {{-- Check Authorization Start --}}
            @if (Auth::check() && Auth::user())
                <a href="{{ route('dashboard') }}"
                    class="h-8 rounded-full px-3 text-current flex items-center justify-center nav-link-drawer">Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="h-8 rounded-full px-3 text-current flex items-center justify-center nav-link-drawer">Login
                </a>
            @endif
            {{-- Google Translate Start --}}
            <div>
                {{-- Google Translate Trigger Button (gambar Google Translate) --}}
                <button id="translateButton" class="border-none bg-none mt-2 p-0">
                    <img src="https://www.google.com/images/icons/product/translate-32.png" alt="Google Translate"
                        style="cursor: pointer;">
                </button>
                {{-- Google Translate Widget --}}
                <div class="relative">
                    <div id="google_translate_element" class="hidden absolute z-10 text-center -bottom-28 -left-72">
                    </div>
                </div>
            </div>
            {{-- Google Translate End --}}
        </nav>
    </div>
    {{-- Header Section End --}}

    <main class="overflow-hidden">

        {{-- Hero Section Start --}}
        <section id="beranda"
            class="relative w-full text-current overflow-hidden flex flex-col items-center justify-center text-white content">
            <div class="absolute z-10 top-0 w-full h-1/2 bg-gradient-to-b from-primary/30 to-primary/0"></div>
            <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop"
                poster="/assets/images/videobg.jpg" class="absolute inset-0 w-full h-full object-cover brightness-75">
                <source src="video/video.mp4" type="video/mp4">
            </video>
            <div class="relative pt-48 pb-32 w-full flex items-center max-w-screen-xl">
                <div class="relative w-full h-full text-center md:text-left">
                    <div class="px-5">
                        <div
                            class="inline-block px-4 py-1 mb-4 border border-white/20 bg-white/10 rounded-full backdrop-blur-md">
                            <p class="text-xs font-medium uppercase tracking-widest text-primary-light">Admin &
                                Operator Portal</p>
                        </div>
                        <h1 class="font-bold text-3xl md:text-6xl leading-tight slide-up">
                            Selamat Datang di Parqeer Internal Management System
                        </h1>
                        <p class="md:text-lg mt-6 leading-relaxed slide-up max-w-2xl opacity-80">
                            Sistem ini dirancang untuk memudahkan Petugas
                            dalam validasi, Owner dalam pemantauan profit, dan Admin dalam konfigurasi sistem parkir
                            digital.
                        </p>
                    </div>
                </div>
            </div>
            <div class="w-full">
                <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                    <defs>
                        <path id="gentle-wave"
                            d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                    </defs>
                    <g class="parallax">
                        <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7" />
                        <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                        <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                        <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
                    </g>
                </svg>
            </div>
        </section>
        {{-- Hero Section End --}}
    </main>


    {{-- JS Start --}}
    <script src="https://website-widgets.pages.dev/dist/sienna.min.js" defer></script>

    {{-- Prelader Start --}}
    <script>
        var loader = document.getElementById('preloader');

        window.addEventListener('load', function() {
            loader.style.display = 'none';
        });
    </script>
    {{-- Prelader End --}}

    {{-- Cursor Start --}}
    <script>
        var cursor = document.querySelector('.cursor');
        var cursorinner = document.querySelector('.cursor2');
        var a = document.querySelectorAll('a');

        document.addEventListener('mousemove', function(e) {
            var x = e.clientX;
            var y = e.clientY;
            cursor.style.transform = `translate3d(calc(${e.clientX}px - 50%), calc(${e.clientY}px - 50%), 0)`
        });

        document.addEventListener('mousemove', function(e) {
            var x = e.clientX;
            var y = e.clientY;
            cursorinner.style.left = x + 'px';
            cursorinner.style.top = y + 'px';
        });

        document.addEventListener('mousedown', function() {
            cursor.classList.add('click');
            cursorinner.classList.add('cursorinnerhover')
        });

        document.addEventListener('mouseup', function() {
            cursor.classList.remove('click')
            cursorinner.classList.remove('cursorinnerhover')
        });

        a.forEach(item => {
            item.addEventListener('mouseover', () => {
                cursor.classList.add('hover');
            });
            item.addEventListener('mouseleave', () => {
                cursor.classList.remove('hover');
            });
        })
    </script>
    {{-- Cursor End --}}

    {{-- Scroll To Top $ Scroll Progress Start --}}
    <script type="text/javascript">
        const scrollButton = document.querySelector('.scroll-top-btn');
        const circle = document.querySelector('.progress-ring__circle');
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;

        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference;

        function setProgress(percent) {
            const offset = circumference - (percent / 100 * circumference);
            circle.style.strokeDashoffset = offset;
        }

        function updateScroll() {
            const scrollTotal = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = window.scrollY;
            const scrollPercent = (scrolled / scrollTotal) * 100;

            setProgress(scrollPercent);

            if (scrolled > 300) {
                scrollButton.style.display = 'flex';
            } else {
                scrollButton.style.display = 'none';
            }
        }

        scrollButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        window.addEventListener('scroll', updateScroll);
    </script>
    {{-- Scroll To Top $ Scroll Progress End --}}

    {{-- Google Translate Widget Start --}}
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL,
                autoDisplay: false
            }, 'google_translate_element');
        }

        document.addEventListener('scroll', function() {
            var translateElement = document.getElementById("google_translate_element");

            // Sembunyikan elemen saat halaman dimuat
            translateElement.style.display = "none";
        });

        document.getElementById("translateButton").onclick = function() {
            var translateElement = document.getElementById("google_translate_element");

            // Tampilkan elemen ketika tombol diklik
            if (translateElement.style.display === "none") {
                translateElement.style.display = "block";

                // Posisi elemen di bawah tombol dan digeser ke kiri sedikit
                var buttonRect = this.getBoundingClientRect();
            } else {
                translateElement.style.display = "none"; // Sembunyikan jika diklik lagi
            }
        };
    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>
    {{-- Google Translate Widget Start End --}}

    {{-- Background Navbar Start --}}
    <script>
        function onScroll() {
            const header = document.getElementById("header");
            //const navbarTop = document.getElementById("navbar-top");
            const nav = document.getElementById("nav");
            if (window.scrollY > 0) {
                // Update header
                header.classList.add("bg-base", "text-dark", "py-5", "shadow-lg");
                header.classList.remove("text-white", "py-12");

                // Update nav
                nav.classList.add("text-gray-500");
                nav.classList.remove("text-white");

                // Update navbarTop
                //navbarTop.classList.add("bg-base", "text-dark", "py-3");
                //navbarTop.classList.remove("text-white", "py-5");

            } else {
                // Kembali ke kondisi awal ketika scroll berada di atas
                header.classList.add("text-white", "py-12");
                header.classList.remove("bg-base", "py-5", "text-dark", "shadow-lg");

                // Update nav
                nav.classList.remove("text-gray-500");

                // Update navbarTop
                //navbarTop.classList.remove("bg-base", "text-dark", "py-3");
                //navbarTop.classList.add("text-white", "py-5");
            }
        }


        document.addEventListener("scroll", onScroll)
        document.addEventListener("DOMContentLoaded", onScroll);
    </script>
    {{-- Background Navbar End --}}

    {{-- Nav Link Start --}}
    <script>
        function navbar() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-link');
            if (window.scrollY > 0) {
                sections.forEach(section => {
                    let top = window.scrollY;
                    let offset = section.offsetTop - 150;
                    let height = section.offsetHeight;
                    let id = section.getAttribute('id');
                    if (top >= offset && top < offset + height) {
                        navLinks.forEach(link => {
                            link.classList.remove('text-primary');
                            document.querySelector('.nav-link[href*=' + id + ']').classList.add(
                                'text-primary');
                        });
                    }
                });
            } else {
                navLinks.forEach(link => {
                    link.classList.remove('text-primary');
                });
            }

        };

        document.addEventListener("scroll", navbar)
    </script>
    {{-- Nav Link End --}}

    {{-- Hidden First Navbar Start
    <script>
        // JavaScript untuk menangani scroll event dan efek navbar pertama
        let lastScrollTop = 0;
        const navbarTop = document.getElementById('navbar-top');
        const navbarHeader = document.getElementById('header');

        window.addEventListener('scroll', function() {
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            // Jika scroll ke bawah, sembunyikan navbar pertama, jika scroll ke atas, tampilkan navbar pertama
            if (currentScroll > lastScrollTop) {
                navbarTop.classList.add('hidden-navbar'); // Menyembunyikan navbar pertama
                navbarHeader.classList.add('navbar-second-fixed'); // Navbar kedua pindah ke atas
            } else {
                navbarTop.classList.remove('hidden-navbar'); // Menampilkan navbar pertama
                navbarHeader.classList.remove('navbar-second-fixed'); // Navbar kedua kembali ke posisi semula
            }

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Untuk mencegah nilai negatif
        });
    </script>
    Hidden First Navbar End --}}

    {{-- Drawer Start --}}
    <script>
        const drawer = document.getElementById("drawer")
        const drawerButton = document.getElementById("header-drawer-button")

        drawerButton.addEventListener('click', function() {
            drawer.classList.toggle('open');
            drawerButton.classList.toggle('open');
        });

        function drawerNavbar() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-link-drawer');
            if (window.scrollY > 0) {
                sections.forEach(section => {
                    let top = window.scrollY;
                    let offset = section.offsetTop - 150;
                    let height = section.offsetHeight;
                    let id = section.getAttribute('id');
                    if (top >= offset && top < offset + height && top !== 0) {
                        navLinks.forEach(link => {
                            link.classList.remove('text-primary');
                            document.querySelector('.nav-link-drawer[href*=' + id + ']').classList.add(
                                'text-primary');
                        });
                    }
                });
            } else {
                navLinks.forEach(link => {
                    link.classList.remove('text-primary');
                });
            }
        };

        document.addEventListener("scroll", drawerNavbar)
    </script>
    {{-- Drawer End --}}

    {{-- Scroll Reveal Start --}}
    <script>
        ScrollReveal().reveal(".slide-up", {
            duration: 1000,
            interval: 200,
            origin: "bottom", // Arah munculnya animasi (bisa 'top', 'left', 'right', atau 'bottom')
            distance: "100px", // Jarak elemen bergerak dalam animasi
            easing: "ease-in-out", // Gaya easing animasi
            reset: false, // Hanya animasi saat pertama kali terlihat
        });

        ScrollReveal().reveal(".slide-right", {
            duration: 1000,
            interval: 200,
            origin: "left", // Arah munculnya animasi (bisa 'top', 'left', 'right', atau 'bottom')
            distance: "100px", // Jarak elemen bergerak dalam animasi
            easing: "ease-in-out", // Gaya easing animasi
            reset: false, // Hanya animasi saat pertama kali terlihat
        });
    </script>
    {{-- Scroll Reveal End --}}
</body>

</html>
