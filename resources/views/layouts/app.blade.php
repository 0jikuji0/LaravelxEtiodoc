<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laravel')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    {{-- Dark mode auto-detection --}}
    <script>
        if (
            localStorage.theme === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            :root {
                --tw-bg-opacity: 1;
                --tw-text-opacity: 1;
            }
            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: rgb(243 244 246 / var(--tw-bg-opacity)); /* light */
                color: rgb(17 24 39 / var(--tw-text-opacity)); /* dark text */
            }
            .dark body {
                background-color: rgb(17 24 39 / var(--tw-bg-opacity)); /* dark */
                color: rgb(243 244 246 / var(--tw-text-opacity)); /* light text */
            }
            h1 {
                font-size: 2rem;
                font-weight: 600;
                margin-bottom: 1rem;
            }
            .container {
                max-width: 640px;
                margin: 3rem auto;
                padding: 1rem;
            }
        </style>
    @endif
</head>
<body>
    <main class="container">
        @yield('content')
    </main>
</body>
</html>
