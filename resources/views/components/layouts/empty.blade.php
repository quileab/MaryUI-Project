<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
    <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="../background.svg" />
    <x-main>
        <x-slot:content>
            <div class="mx-auto w-72 bg-slate-800 bg-opacity-30 backdrop-blur-sm rounded-lg shadow-sm shadow-black p-4">
                <div class="my-4 text-center">
                {{-- logo --}}
                <img src="../logo.webp" class="w-1/3 mx-auto">
                </div>
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>
</body>
</html>