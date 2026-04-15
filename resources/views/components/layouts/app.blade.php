<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-stone-50 text-stone-900 dark:bg-stone-950 dark:text-stone-100 min-h-screen antialiased isolate selection:bg-teal-700 selection:text-stone-50"
          x-data="{
              dark: document.documentElement.classList.contains('dark'),
              toggle() {
                  this.dark = !this.dark;
                  localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                  document.documentElement.classList.toggle('dark', this.dark);
              }
          }">
        <div class="grain fixed inset-0 pointer-events-none -z-10"></div>

        <header class="border-b border-stone-200 dark:border-stone-800 bg-stone-50 dark:bg-stone-950 sticky top-0 z-30">
            <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between gap-10">
                <a href="{{ route('shorten') }}" wire:navigate
                   class="flex items-center gap-2.5 group">
                    <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-full bg-teal-700 dark:bg-teal-500 text-stone-50 text-sm font-bold transition-transform group-hover:-rotate-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                        </svg>
                    </span>
                    <span class="font-display text-2xl font-semibold tracking-tight">Shorty</span>
                    <span class="hidden sm:inline text-[11px] uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400 font-medium ml-1">— link press</span>
                </a>

                <nav class="flex items-center gap-1">
                    @php
                        $navItems = [
                            ['route' => 'shorten', 'label' => 'Shorten'],
                            ['route' => 'history', 'label' => 'History'],
                            ['route' => 'about', 'label' => 'About'],
                        ];
                    @endphp
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" wire:navigate
                           class="relative px-3 py-1.5 text-sm font-medium transition-colors
                                  {{ request()->routeIs($item['route'])
                                     ? 'text-teal-700 dark:text-teal-400'
                                     : 'text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100' }}">
                            {{ $item['label'] }}
                            @if (request()->routeIs($item['route']))
                                <span class="absolute -bottom-[17px] left-3 right-3 h-[2px] bg-teal-700 dark:bg-teal-400"></span>
                            @endif
                        </a>
                    @endforeach

                    <button type="button"
                            x-on:click="toggle()"
                            :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
                            class="ml-2 p-2 rounded-full text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 hover:bg-stone-100 dark:hover:bg-stone-800 transition">
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                        <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                    </button>
                </nav>
            </div>
        </header>

        <flux:main class="relative">
            @if (session('flash'))
                <div class="max-w-3xl mx-auto px-6 mt-6 animate-rise">
                    <div class="flex items-start gap-3 bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-900 text-teal-900 dark:text-teal-200 px-4 py-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('flash') }}</span>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </flux:main>

        <footer class="mt-20 border-t border-stone-200/70 dark:border-stone-800/70">
            <div class="max-w-5xl mx-auto px-6 py-8 flex items-center justify-between text-xs text-stone-500 dark:text-stone-500">
                <span class="font-mono">SHORTY <span class="text-stone-400 dark:text-stone-600">·</span> {{ now()->year }}</span>
                <span class="uppercase tracking-[0.18em]">No tracking <span class="text-stone-300 dark:text-stone-700 mx-1.5">/</span> No accounts</span>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
