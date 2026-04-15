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

        session()->flash('flash', "Link created: {$slug}");
        $this->redirect('/history', navigate: true);
    }
}; ?>

<div class="max-w-2xl mx-auto p-4">
    <h1 class="font-bold text-2xl text-slate-900 mb-4">Shorten a link</h1>

    <form wire:submit="save" class="space-y-4 bg-white border border-slate-200 rounded-lg p-6">
        <div>
            <label for="url" class="block text-sm font-medium text-slate-700 mb-1">
                Long URL
            </label>
            <input
                type="url"
                id="url"
                wire:model="url"
                placeholder="https://example.com/very/long/path"
                class="w-full border border-slate-200 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500"
                required
            >
            @error('url')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-slate-700 mb-1">
                Custom slug <span class="text-slate-500">(optional)</span>
            </label>
            <input
                type="text"
                id="slug"
                wire:model="slug"
                placeholder="my-link"
                class="w-full border border-slate-200 rounded px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-teal-500"
            >
            @error('slug')
                <p class="mt-1 text-sm text-amber-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded font-medium disabled:opacity-50"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Shorten</span>
            <span wire:loading>Shortening…</span>
        </button>
    </form>
</div>
