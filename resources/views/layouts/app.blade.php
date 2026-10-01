<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563EB">
    <title>@yield('title', 'CMSStaging Monitoring')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    <a class="skip-link" href="#main-content">Langsung ke konten</a>
    @php($exportMode = request()->routeIs('history') && request('view') === 'export')
    <aside class="sidebar" aria-label="Navigasi utama">
        <div class="brand">
            <span class="brand-mark"><x-icon name="xyz" class="h-6 w-6" /></span>
            <div><h1>CMSStaging<span class="text-blue-600">.</span></h1><p>MONITORING DEVICE</p></div>
            <button class="mobile-menu button-secondary" type="button" aria-expanded="false" aria-controls="sidebar-menu" aria-label="Buka menu navigasi"><x-icon name="menu" /></button>
        </div>
        <div class="sidebar-content" id="sidebar-menu">
            <p class="nav-caption">RUANG KERJA</p>
            <nav class="flex flex-1 flex-col gap-1">
                <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif class="nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"><x-icon name="dashboard" />Dashboard</a>
                <a href="{{ route('devices') }}" @if(request()->routeIs('devices')) aria-current="page" @endif class="nav-link {{ request()->routeIs('devices') ? 'is-active' : '' }}"><x-icon name="devices" />Devices</a>
                <a href="{{ route('history') }}" @if(request()->routeIs('history') && !$exportMode) aria-current="page" @endif class="nav-link {{ request()->routeIs('history') && !$exportMode ? 'is-active' : '' }}"><x-icon name="history" />History</a>
                <a href="{{ route('history', ['view' => 'export']) }}" @if($exportMode) aria-current="page" @endif class="nav-link {{ $exportMode ? 'is-active' : '' }}"><x-icon name="export" />Export</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="workspace-label"><span class="status-dot"></span><span>CMSStaging<small>Panel pemantauan</small></span></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="nav-link w-full"><x-icon name="logout" />Logout</button></form>
            </div>
        </div>
    </aside>
    <div class="main-shell">
        <header class="topbar"><div class="flex items-center gap-2"><span class="text-gray-500">Ruang kerja</span><span class="text-gray-300">/</span><span class="font-medium">@yield('page-label', 'Dashboard')</span></div><span class="topbar-date"><x-icon name="calendar" class="h-4 w-4" />{{ now()->locale('id')->translatedFormat('d F Y') }}</span></header>
        <main id="main-content" tabindex="-1">@yield('content')</main>
        <footer class="page-footer"><span>CMSStaging · Monitoring device</span><span>Waktu ditampilkan dalam {{ config('app.timezone') }}</span></footer>
    </div>
    @stack('scripts')
</body>
</html>
