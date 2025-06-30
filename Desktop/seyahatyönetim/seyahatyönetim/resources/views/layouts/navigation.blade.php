<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Seyahat Giderleri Takip ve Yönetim Sistemiu')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=1">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=1">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .page-header {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .header-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        .sidebar {
            width: 250px;
            background: #ffffff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
        }
        .sidebar ul li a:hover {
            background: #f97316;
            color: white;
        }
        .content-wrapper {
            margin-left: 250px;
        }
    </style>
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="sidebar">
        @php
            $user = Auth::user();
        @endphp

        <ul>
            <li>
                <a href="{{ route('dashboard') }}">Genel Bakış</a>
            </li>
            <li>
                <a href="{{ route('giderler') }}">Giderler</a>
            </li>

            @if ($user && $user->role === 'admin')
                <li class="mt-4 text-xs text-gray-500 uppercase">Yönetici</li>

                <li>
                    <a href="{{ route('users.index') }}">Kullanıcılar</a>
                </li>
                <li>
                    <a href="{{ route('roles.index') }}">Roller</a>
                </li>
                <li>
                    <a href="{{ route('permissions.index') }}">İzinler</a>
                </li>
            @endif
        </ul>
    </aside>

    <!-- İçerik Alanı -->
    <div class="content-wrapper flex-1">
        @include('layouts.navigation')

        <header class="page-header shadow">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="header-content">
                    <img src="{{ asset('images/favicon.png') }}?v=1" alt="Platform Logosu" class="header-icon">
                    <h1 class="page-title">
                        {{ $header ?? 'Seyahat Giderleri Takip ve Yönetim Sistemi' }}
                    </h1>
                </div>

                @isset($subheader)
                    <p class="mt-2 text-yellow-100">{{ $subheader }}</p>
                @endisset
            </div>
        </header>

        <main class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
</body>
</html>
