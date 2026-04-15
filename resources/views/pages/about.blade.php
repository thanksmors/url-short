<x-layouts.app>
    <div class="max-w-4xl mx-auto p-4 space-y-6">
        <h1 class="font-bold text-2xl">About Shorty</h1>

        <p class="text-base text-slate-700">
            Shorty is the simplest possible link shortener. Paste a URL, get a short slug,
            share it. No accounts, no tracking, no ceremony.
        </p>

        <section>
            <h2 class="font-bold text-xl mb-2">How it works</h2>
            <ol class="list-decimal list-inside space-y-1 text-slate-700">
                <li>Paste a long URL on the <a href="{{ route('shorten') }}" wire:navigate class="text-teal-600 underline">Shorten</a> page.</li>
                <li>Get back a short link like <code class="font-mono text-teal-600">/r/abc123</code>.</li>
                <li>Share it anywhere — visitors get redirected to the original URL.</li>
            </ol>
        </section>

        <section>
            <h2 class="font-bold text-xl mb-2">Tech stack</h2>
            <p class="text-slate-700">Built with Laravel, Livewire, Volt, Tailwind, and SQLite. Hosted on Laravel Cloud.</p>
        </section>

        <p class="text-sm text-slate-500">
            Source: <a href="https://github.com/" class="underline">GitHub</a>
        </p>
    </div>
</x-layouts.app>
