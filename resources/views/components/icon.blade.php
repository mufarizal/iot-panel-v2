@props(['name', 'class' => 'h-5 w-5'])
<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('dashboard') <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/> @break
        @case('devices') <rect x="4" y="4" width="16" height="13" rx="2"/><path d="M8 21h8m-4-4v4M7 11h3l2-4 2 7 2-3h1"/> @break
        @case('history') <path d="M3 11a9 9 0 1 1 2.7 7M3 4v7h7m2-4v5l3 2"/> @break
        @case('export') <path d="M12 3v12m-4-4 4 4 4-4M4 16v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4"/> @break
        @case('logout') <path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4m6-13 5 5-5 5m-6-5h11"/> @break
        @case('arrow') <path d="M5 12h14m-5-5 5 5-5 5"/> @break
        @case('current') <path d="m13 2-9 12h7l-1 8 10-13h-8l1-7Z"/> @break
        @case('temp_humidity') <path d="M9 14.8V5a3 3 0 0 1 6 0v9.8a5 5 0 1 1-6 0Z"/><path d="M12 9v9m6-12h3m-3 4h2"/> @break
        @case('xyz') <path d="M3 12h4l3-8 4 16 3-8h4"/> @break
        @case('menu') <path d="M4 6h16M4 12h16M4 18h16"/> @break
        @case('calendar') <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 11h18"/> @break
    @endswitch
</svg>
