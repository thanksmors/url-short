<x-layouts.app>
    <div class="max-w-2xl mx-auto p-8 text-center">
        <h1 class="font-bold text-2xl text-slate-900 mb-2">Link not found</h1>
        <p class="text-slate-500">This short link doesn't exist or has been deleted.</p>
        <a href="{{ route('shorten') }}" wire:navigate
           class="inline-block mt-4 text-teal-600 underline">Create a new one</a>
    </div>
</x-layouts.app>
