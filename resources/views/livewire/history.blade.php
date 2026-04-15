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

<div class="max-w-4xl mx-auto p-4">
    <h1 class="font-bold text-2xl text-slate-900 mb-4">Your links</h1>

    @if ($links->isEmpty())
        <div class="bg-white border border-slate-200 rounded-lg p-8 text-center">
            <p class="text-slate-500">No links yet — create one above!</p>
            <a href="{{ route('shorten') }}" wire:navigate
               class="inline-block mt-3 text-teal-600 underline">
                Go to Shorten
            </a>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-200">
            @foreach ($links as $link)
                <div
                    wire:key="link-{{ $link->id }}"
                    x-data="{ copied: false }"
                    class="p-4 flex items-center justify-between gap-4 transition-opacity duration-200"
                >
                    <div class="flex-1 min-w-0">
                        <div class="font-mono text-teal-600">/r/{{ $link->slug }}</div>
                        <div class="text-sm text-slate-500 truncate">{{ $link->original_url }}</div>
                        <div class="text-xs text-slate-400">{{ $link->created_at->format('M j, Y g:i a') }}</div>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <button
                            type="button"
                            x-on:click="
                                navigator.clipboard.writeText(window.location.origin + '/r/{{ $link->slug }}');
                                copied = true;
                                setTimeout(() => copied = false, 1500);
                            "
                            class="px-3 py-1.5 text-sm border border-slate-200 rounded hover:bg-slate-50"
                        >
                            <span x-show="!copied">Copy</span>
                            <span x-show="copied" x-cloak class="text-teal-600">✓ Copied</span>
                        </button>
                        <button
                            type="button"
                            wire:click="delete({{ $link->id }})"
                            wire:confirm="Delete this link?"
                            class="px-3 py-1.5 text-sm border border-red-200 text-red-600 rounded hover:bg-red-50"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
