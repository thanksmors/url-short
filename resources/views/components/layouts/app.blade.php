<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-slate-50 text-slate-900 min-h-screen">
        <nav class="bg-white border-b border-slate-200">
            <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
                <a href="{{ route('shorten') }}" wire:navigate class="font-bold text-slate-900">
                    🔗 Shorty
                </a>
                <div class="flex gap-6 text-sm">
                    <a href="{{ route('shorten') }}" wire:navigate
                       class="{{ request()->routeIs('shorten') ? 'text-teal-600' : 'text-slate-600 hover:text-slate-900' }}">
                        Shorten
                    </a>
                    <a href="{{ route('history') }}" wire:navigate
                       class="{{ request()->routeIs('history') ? 'text-teal-600' : 'text-slate-600 hover:text-slate-900' }}">
                        History
                    </a>
                    <a href="{{ route('about') }}" wire:navigate
                       class="{{ request()->routeIs('about') ? 'text-teal-600' : 'text-slate-600 hover:text-slate-900' }}">
                        About
                    </a>
                </div>
            </div>
        </nav>

        <flux:main>
            @if (session('flash'))
                <div class="max-w-4xl mx-auto px-4 mt-4">
                    <div class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 rounded">
                        {{ session('flash') }}
                    </div>
                </div>
            @endif

            {{ $slot }}
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
