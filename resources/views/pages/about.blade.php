<x-layouts.app>
    <div class="max-w-3xl mx-auto px-6 pt-16 pb-10">
        <div class="flex items-center gap-3 mb-8 animate-rise">
            <span class="h-px w-8 bg-stone-300 dark:bg-stone-700"></span>
            <span class="text-[11px] uppercase tracking-[0.22em] text-stone-500 dark:text-stone-400 font-medium">
                Colophon &middot; About Shorty
            </span>
        </div>

        <h1 class="font-display text-5xl md:text-6xl font-semibold leading-[1.05] tracking-tight text-stone-900 dark:text-stone-50 animate-rise" style="animation-delay: 60ms">
            A small press for
            <span class="italic font-normal text-stone-400 dark:text-stone-500">long links.</span>
        </h1>

        <div class="mt-10 prose-like text-lg leading-relaxed text-stone-700 dark:text-stone-300 animate-rise" style="animation-delay: 120ms">
            <p>
                <span class="font-display text-6xl float-left mr-3 mt-2 leading-none text-teal-700 dark:text-teal-400 font-semibold">S</span>horty is the simplest possible link shortener. Paste a URL, get a short slug, share it. No accounts, no tracking, no ceremony &mdash; the software stays out of your way so you can get on with sending the link.
            </p>
            <p class="mt-6">
                It was built as a demonstration of restraint: a single idea expressed without the usual web-app baggage. You won't find analytics, QR codes, or a pricing page here.
            </p>
        </div>

        <hr class="my-16 border-stone-200 dark:border-stone-800 animate-rise" style="animation-delay: 180ms">

        <section class="grid md:grid-cols-[180px_1fr] gap-8 md:gap-12 animate-rise" style="animation-delay: 240ms">
            <h2 class="font-display text-xl font-semibold tracking-tight text-stone-900 dark:text-stone-50 md:text-right md:border-r md:border-stone-200 md:dark:border-stone-800 md:pr-12">
                How it works
            </h2>
            <ol class="space-y-5">
                <li class="flex gap-4">
                    <span class="font-mono text-xs tabular-nums text-stone-400 dark:text-stone-600 pt-1.5 flex-shrink-0">01</span>
                    <p class="text-stone-700 dark:text-stone-300">
                        Paste a long URL on the
                        <a href="{{ route('shorten') }}" wire:navigate class="text-teal-700 dark:text-teal-400 underline underline-offset-4 decoration-teal-700/30 dark:decoration-teal-400/30 hover:decoration-teal-700 dark:hover:decoration-teal-400">Shorten</a>
                        page.
                    </p>
                </li>
                <li class="flex gap-4">
                    <span class="font-mono text-xs tabular-nums text-stone-400 dark:text-stone-600 pt-1.5 flex-shrink-0">02</span>
                    <p class="text-stone-700 dark:text-stone-300">
                        Get back a short slug like <code class="font-mono text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/40 px-1.5 py-0.5 rounded">/r/abc123</code>.
                    </p>
                </li>
                <li class="flex gap-4">
                    <span class="font-mono text-xs tabular-nums text-stone-400 dark:text-stone-600 pt-1.5 flex-shrink-0">03</span>
                    <p class="text-stone-700 dark:text-stone-300">
                        Share it anywhere. Visitors hit the redirect and arrive at the original URL instantly.
                    </p>
                </li>
            </ol>
        </section>

        <hr class="my-16 border-stone-200 dark:border-stone-800 animate-rise" style="animation-delay: 300ms">

        <section class="grid md:grid-cols-[180px_1fr] gap-8 md:gap-12 animate-rise" style="animation-delay: 360ms">
            <h2 class="font-display text-xl font-semibold tracking-tight text-stone-900 dark:text-stone-50 md:text-right md:border-r md:border-stone-200 md:dark:border-stone-800 md:pr-12">
                Colophon
            </h2>
            <dl class="font-mono text-sm space-y-3 text-stone-700 dark:text-stone-300">
                <div class="flex gap-4">
                    <dt class="w-24 text-stone-400 dark:text-stone-600 uppercase tracking-[0.14em] text-[11px] pt-1">Framework</dt>
                    <dd>Laravel 11 &middot; Livewire 3 &middot; Volt</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-24 text-stone-400 dark:text-stone-600 uppercase tracking-[0.14em] text-[11px] pt-1">UI</dt>
                    <dd>Flux &middot; Tailwind &middot; Alpine</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-24 text-stone-400 dark:text-stone-600 uppercase tracking-[0.14em] text-[11px] pt-1">Storage</dt>
                    <dd>SQLite</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-24 text-stone-400 dark:text-stone-600 uppercase tracking-[0.14em] text-[11px] pt-1">Hosting</dt>
                    <dd>Laravel Cloud</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-24 text-stone-400 dark:text-stone-600 uppercase tracking-[0.14em] text-[11px] pt-1">Type</dt>
                    <dd>Fraunces &middot; Instrument Sans &middot; JetBrains Mono</dd>
                </div>
            </dl>
        </section>

        <div class="mt-16 animate-rise" style="animation-delay: 420ms">
            <a href="https://github.com/thanksmors/url-short" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 text-sm text-stone-600 dark:text-stone-400 hover:text-teal-700 dark:hover:text-teal-400 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.463 2 11.97c0 4.404 2.865 8.14 6.839 9.458.5.092.682-.216.682-.48 0-.236-.008-.864-.013-1.695-2.782.602-3.369-1.337-3.369-1.337-.454-1.151-1.11-1.458-1.11-1.458-.908-.618.069-.606.069-.606 1.003.07 1.531 1.027 1.531 1.027.892 1.524 2.341 1.084 2.91.828.092-.643.35-1.083.636-1.332-2.22-.251-4.555-1.107-4.555-4.927 0-1.088.39-1.979 1.029-2.675-.103-.252-.446-1.266.098-2.638 0 0 .84-.268 2.75 1.022A9.606 9.606 0 0 1 12 6.82c.85.004 1.705.114 2.504.336 1.909-1.29 2.747-1.022 2.747-1.022.546 1.372.202 2.386.1 2.638.64.696 1.028 1.587 1.028 2.675 0 3.83-2.339 4.673-4.566 4.92.359.307.678.915.678 1.846 0 1.332-.012 2.407-.012 2.734 0 .267.18.577.688.48C19.137 20.107 22 16.373 22 11.969 22 6.463 17.522 2 12 2Z" />
                </svg>
                View source on GitHub
            </a>
        </div>
    </div>
</x-layouts.app>
