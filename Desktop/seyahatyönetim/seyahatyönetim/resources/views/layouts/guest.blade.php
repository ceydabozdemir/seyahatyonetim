<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Seyahat Giderleri Takip ve Yönetim Sistemi') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Raleway:wght@400;600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

    <!-- Logo ve Başlık Düzeni -->
    <div class="flex items-center justify-center space-x-4 mb-6">
        <!-- Logo -->
        <a href="/">
            <x-application-logo class="w-16 h-16 fill-current text-gray-500" />
        </a>

        <!-- Başlık -->
        <div class="text-center">
            <div class="text-4xl font-semibold text-gray-800" style="font-family: 'Poppins', sans-serif;">
                <span class="text-indigo-600">Seyahat Giderleri</span>
            </div>
            <div class="text-2xl font-semibold text-gray-800" style="font-family: 'Raleway', sans-serif;">
                Takip ve Yönetim Sistemi
            </div>
        </div>
    </div>

    <!-- Diğer İçerikler -->
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
</body>
</html>
