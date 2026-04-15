<?php

use App\Models\Link;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function delete(int $id): void
    {
        Link::whereKey($id)->delete();
    }

    public function with(): array
    {
        return [
            'links' => Link::orderByDesc('created_at')->get(),
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto px-6 pt-16 pb-10">
    <div class="flex items-end justify-between mb-10 animate-rise">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="h-px w-8 bg-stone-300 dark:bg-stone-700"></span>
                <span class="text-[11px] uppercase tracking-[0.22em] text-stone-500 dark:text-stone-400 font-medium">
                    The Archive &middot; History
                </span>
            </div>
            <h1 class="font-display text-4xl md:text-5xl font-semibold tracking-tight">Your links</h1>
        </div>
        <div class="hidden md:block text-right">
            <div class="font-mono text-sm text-stone-500 dark:text-stone-400">{{ $links->count() }} {{ Str::plural('link', $links->count()) }}</div>
            <div class="text-[11px] uppercase tracking-[0.18em] text-stone-400 dark:text-stone-600 mt-1">Newest first</div>
        </div>
    </div>

    @if ($links->isEmpty())
        <div class="border-2 border-dashed border-stone-300 dark:border-stone-700 rounded-xl p-16 text-center animate-rise" style="animation-delay: 60ms">
            <div class="w-16 h-16 mx-auto rounded-full bg-stone-100 dark:bg-stone-900 flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-stone-400 dark:text-stone-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                </svg>
            </div>
            <h2 class="font-display text-2xl font-semibold text-stone-900 dark:text-stone-100">No links yet</h2>
            <p class="mt-2 text-stone-500 dark:text-stone-400 max-w-sm mx-auto">
                The archive is empty. Press your first link and it will appear here, newest on top.
            </p>
            <a href="{{ route('shorten') }}" wire:navigate
               class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 bg-stone-900 dark:bg-stone-100 text-stone-50 dark:text-stone-900 rounded-full text-sm font-medium hover:bg-teal-700 dark:hover:bg-teal-400 transition-colors">
                Press a link
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    @else
        <ol class="space-y-px">
            @foreach ($links as $i => $link)
                <li
                    wire:key="link-{{ $link->id }}"
                    x-data="{ copied: false }"
                    class="group relative grid grid-cols-[auto_1fr_auto] items-center gap-5 py-5 border-t border-stone-200 dark:border-stone-800 {{ $loop->last ? 'border-b' : '' }} hover:bg-white dark:hover:bg-stone-900/50 transition-colors animate-rise"
                    style="animation-delay: {{ min($i * 40, 400) }}ms"
                >
                    <div class="font-mono text-xs text-stone-400 dark:text-stone-600 tabular-nums w-8">
                        {{ str_pad($links->count() - $i, 2, '0', STR_PAD_LEFT) }}
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-baseline gap-2">
                            <span class="font-mono text-base text-stone-400 dark:text-stone-600">/r/</span>
                            <a href="{{ route('redirect', $link->slug) }}" target="_blank" rel="noopener"
                               class="font-mono text-lg font-medium text-teal-700 dark:text-teal-400 hover:underline underline-offset-4 decoration-teal-700/30 dark:decoration-teal-400/30">{{ $link->slug }}</a>
                        </div>
                        <div class="mt-1 text-sm text-stone-600 dark:text-stone-400 truncate" title="{{ $link->original_url }}">
                            {{ $link->original_url }}
                        </div>
                        <div class="mt-1 text-[11px] uppercase tracking-[0.14em] text-stone-400 dark:text-stone-600 font-mono">
                            {{ $link->created_at->format('M j, Y') }}
                            <span class="text-stone-300 dark:text-stone-700 mx-1">/</span>
                            {{ $link->created_at->format('g:i a') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-1 opacity-70 group-hover:opacity-100 transition-opacity">
                        <button
                            type="button"
                            x-on:click="
                                navigator.clipboard.writeText(window.location.origin + '/r/{{ $link->slug }}');
                                copied = true;
                                setTimeout(() => copied = false, 1500);
                            "
                            class="relative flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 transition"
                            :class="copied && '!text-teal-700 dark:!text-teal-400'"
                        >
                            <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                            </svg>
                            <svg x-show="copied" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span x-text="copied ? 'Copied' : 'Copy'"></span>
                        </button>
                        <button
                            type="button"
                            wire:click="delete({{ $link->id }})"
                            wire:confirm="Delete /r/{{ $link->slug }}? This can't be undone."
                            class="p-2 text-stone-400 dark:text-stone-600 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-full transition"
                            aria-label="Delete link"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</div>
