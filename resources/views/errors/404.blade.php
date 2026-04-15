<x-layouts.app>
    <div class="max-w-2xl mx-auto px-6 pt-24 pb-10 text-center">
        <div class="font-mono text-[11px] uppercase tracking-[0.22em] text-stone-500 dark:text-stone-400 animate-rise">
            Error &middot; 404
        </div>

        <h1 class="mt-4 font-display text-6xl md:text-7xl font-semibold tracking-tight animate-rise" style="animation-delay: 60ms">
            Link not found
        </h1>

        <p class="mt-6 text-lg text-stone-600 dark:text-stone-400 animate-rise" style="animation-delay: 120ms">
            This short link doesn't exist, or has been deleted.
            <span class="block text-stone-400 dark:text-stone-600 mt-1 italic">— the press has no memory of it.</span>
        </p>

        <div class="mt-10 flex items-center justify-center gap-3 animate-rise" style="animation-delay: 180ms">
            <a href="{{ route('shorten') }}" wire:navigate
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 dark:bg-stone-100 text-stone-50 dark:text-stone-900 rounded-full text-sm font-medium hover:bg-teal-700 dark:hover:bg-teal-400 transition">
                Press a new link
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <a href="{{ route('history') }}" wire:navigate
               class="px-5 py-2.5 text-sm font-medium text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100 transition">
                View history
            </a>
        </div>
    </div>
</x-layouts.app>
