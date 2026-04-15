<?php

use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $url = '';
    public string $slug = '';

    public function save(): void
    {
        $key = 'create-link:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'url' => "Slow down! Try again in {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 60);

        $request = new StoreLinkRequest();
        $validated = $this->validate($request->rules(), $request->messages());

        $slug = $validated['slug'] ?? null;

        if (! $slug) {
            $attempts = 0;
            do {
                $candidate = Str::random(6);
                $exists = Link::where('slug', $candidate)->exists();
                $attempts++;
            } while ($exists && $attempts < 5);

            if ($exists) {
                abort(500, 'Could not generate a unique slug');
            }

            $slug = $candidate;
        }

        Link::create([
            'slug' => $slug,
            'original_url' => $validated['url'],
        ]);

        session()->flash('flash', "Link created: /r/{$slug}");
        $this->redirect('/history', navigate: true);
    }
}; ?>

<div class="max-w-3xl mx-auto px-6 pt-16 md:pt-24 pb-10">
    <div class="flex items-center gap-3 mb-8 animate-rise">
        <span class="h-px w-8 bg-stone-300 dark:bg-stone-700"></span>
        <span class="text-[11px] uppercase tracking-[0.22em] text-stone-500 dark:text-stone-400 font-medium">
            Issue №{{ str_pad(\App\Models\Link::count() + 1, 3, '0', STR_PAD_LEFT) }} &middot; Shorten
        </span>
    </div>

    <h1 class="font-display text-5xl md:text-7xl font-semibold leading-[1.02] tracking-tight text-stone-900 dark:text-stone-50 animate-rise" style="animation-delay: 60ms">
        Shorten any link
        <span class="block italic font-normal text-stone-400 dark:text-stone-500">in a single press.</span>
    </h1>

    <p class="mt-6 text-lg text-stone-600 dark:text-stone-400 max-w-xl animate-rise" style="animation-delay: 120ms">
        Paste a URL. Get a short one. Share it anywhere. No accounts, no tracking, no ceremony &mdash; just a fast little link press.
    </p>

    <form wire:submit="save" class="mt-12 animate-rise" style="animation-delay: 180ms">
        <div class="flex flex-col md:flex-row md:items-stretch gap-3 md:gap-0 border-2 border-stone-900 dark:border-stone-100 rounded-xl overflow-hidden bg-white dark:bg-stone-900 shadow-[4px_4px_0_0_rgba(28,25,23,0.92)] dark:shadow-[4px_4px_0_0_rgba(250,250,249,0.1)]">
            <input
                type="url"
                id="url"
                wire:model="url"
                placeholder="https://example.com/very/long/path"
                class="flex-1 px-5 py-5 text-lg bg-transparent border-none focus:outline-none placeholder:text-stone-400 dark:placeholder:text-stone-600"
                required
                autofocus
            >
            <button
                type="submit"
                class="md:w-48 px-6 py-4 md:py-0 bg-stone-900 dark:bg-stone-100 text-stone-50 dark:text-stone-900 font-medium text-base hover:bg-teal-700 dark:hover:bg-teal-400 dark:hover:text-stone-900 transition-colors flex items-center justify-center gap-2 border-t-2 md:border-t-0 md:border-l-2 border-stone-900 dark:border-stone-100"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove class="flex items-center gap-2">
                    Shorten
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Pressing&hellip;
                </span>
            </button>
        </div>
        @error('url')
            <p class="mt-3 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                {{ $message }}
            </p>
        @enderror

        <div class="mt-6 flex items-center gap-3 text-sm">
            <label for="slug" class="text-[11px] uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400 font-medium">
                Custom slug
            </label>
            <span class="flex-1 border-t border-dotted border-stone-300 dark:border-stone-700"></span>
            <span class="text-xs text-stone-400 dark:text-stone-500">optional</span>
        </div>
        <div class="mt-2 flex items-baseline gap-2 font-mono text-base">
            <span class="text-stone-400 dark:text-stone-500">/r/</span>
            <input
                type="text"
                id="slug"
                wire:model="slug"
                placeholder="my-link"
                class="flex-1 bg-transparent border-none py-2 text-stone-900 dark:text-stone-100 border-b border-stone-300 dark:border-stone-700 focus:outline-none focus:border-teal-700 dark:focus:border-teal-400 placeholder:text-stone-300 dark:placeholder:text-stone-700"
            >
        </div>
        @error('slug')
            <p class="mt-2 text-sm text-amber-700 dark:text-amber-400">{{ $message }}</p>
        @enderror
    </form>

    <div class="mt-20 pt-8 border-t border-stone-200 dark:border-stone-800 grid grid-cols-3 gap-6 text-center animate-rise" style="animation-delay: 240ms">
        <div>
            <div class="font-display text-3xl font-semibold text-teal-700 dark:text-teal-400">{{ \App\Models\Link::count() }}</div>
            <div class="text-[11px] uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400 mt-1">Links pressed</div>
        </div>
        <div>
            <div class="font-display text-3xl font-semibold">6</div>
            <div class="text-[11px] uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400 mt-1">Chars per slug</div>
        </div>
        <div>
            <div class="font-display text-3xl font-semibold">0</div>
            <div class="text-[11px] uppercase tracking-[0.18em] text-stone-500 dark:text-stone-400 mt-1">Accounts needed</div>
        </div>
    </div>
</div>
