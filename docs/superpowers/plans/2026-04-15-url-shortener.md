# URL Shortener Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a public Laravel URL shortener with three pages (create, history, about) and a redirect route, using the Livewire starter kit and SQLite.

**Architecture:** Laravel 11 app scaffolded from the Livewire starter kit, auth scaffolding removed. Livewire/Volt components for the create form and history list. A plain Blade view for About. A controller action for the redirect route using route model binding by `slug`. SQLite for persistence, Pest for testing.

**Tech Stack:** PHP 8.2+, Laravel 11, Livewire 3 + Volt, Tailwind, Alpine.js, SQLite, Pest.

Spec: `docs/superpowers/specs/2026-04-15-url-shortener-design.md`

---

## Task 1: Scaffold Laravel app with Livewire starter kit

**Files:**
- Create: entire `url-short/` Laravel scaffold
- Modify: `.env` (set `DB_CONNECTION=sqlite`)
- Create: `database/database.sqlite`

- [ ] **Step 1: Create the Laravel project**

Run from `/home/claude-team/mors/laravel/url-short/`:

```bash
cd /home/claude-team/mors/laravel
# Move existing docs out of the way so laravel new can scaffold into url-short/
mv url-short url-short-seed
laravel new url-short --livewire --pest --git --no-interaction
# Bring the spec and plan docs into the new project
mv url-short-seed/docs url-short/
mv url-short-seed/01-url-shortener-design.md url-short/
rmdir url-short-seed
```

Expected: Laravel 11 app created, git repo initialized, Livewire + Volt + Tailwind + Pest installed, auth scaffolding present.

- [ ] **Step 2: Configure SQLite**

Edit `.env`:

```
DB_CONNECTION=sqlite
```

Remove or comment out: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

Create the DB file:

```bash
touch database/database.sqlite
```

- [ ] **Step 3: Run migrations to verify setup**

```bash
php artisan migrate
```

Expected: default migrations run successfully against SQLite.

- [ ] **Step 4: Run the starter kit's tests to verify baseline**

```bash
php artisan test
```

Expected: all starter-kit tests pass.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "chore: scaffold Laravel with Livewire starter kit and SQLite"
```

---

## Task 2: Remove auth scaffolding

**Files:**
- Modify: `routes/web.php`
- Delete: `routes/auth.php`
- Delete: auth Volt components under `resources/views/livewire/auth/`, `resources/views/livewire/settings/`
- Delete: `resources/views/livewire/pages/auth/` if present
- Delete: `database/migrations/*_create_users_table.php`, `*_create_password_reset_tokens_table.php`, `*_create_sessions_table.php` (sessions stays — keep it)
- Delete: `app/Models/User.php`, `app/Http/Controllers/Auth/` if present
- Delete: `tests/Feature/Auth/`, `tests/Feature/Settings/` if present

- [ ] **Step 1: Inspect what the starter kit generated**

```bash
ls routes/
ls resources/views/livewire/
ls database/migrations/
ls tests/Feature/
```

Note the exact filenames of auth-related routes, components, migrations, and tests. The next steps use `rm` with these paths — verify each exists before removing.

- [ ] **Step 2: Remove auth routes**

Edit `routes/web.php`. Remove any `require __DIR__.'/auth.php';` line and any auth-protected route groups. Replace the file contents with a minimal placeholder (real routes added in later tasks):

```php
<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
```

Delete `routes/auth.php`:

```bash
rm routes/auth.php
```

- [ ] **Step 3: Remove auth Volt components and views**

```bash
rm -rf resources/views/livewire/auth
rm -rf resources/views/livewire/settings
rm -rf resources/views/livewire/pages/auth 2>/dev/null || true
rm -f resources/views/dashboard.blade.php
```

- [ ] **Step 4: Remove auth controllers, model, migrations, and tests**

```bash
rm -rf app/Http/Controllers/Auth 2>/dev/null || true
rm -f app/Models/User.php
rm -f database/migrations/*_create_users_table.php
rm -f database/migrations/*_create_password_reset_tokens_table.php
rm -rf tests/Feature/Auth 2>/dev/null || true
rm -rf tests/Feature/Settings 2>/dev/null || true
rm -rf tests/Feature/ProfileTest.php 2>/dev/null || true
```

Keep the `sessions` migration — Laravel uses it for flash messages.

- [ ] **Step 5: Remove auth references from config**

Edit `config/auth.php` — leave as-is (unused code is harmless, and editing risks breakage). Move on.

Edit `app/Providers/AppServiceProvider.php` — if it references `User` or auth, remove those references. Otherwise skip.

- [ ] **Step 6: Simplify the app layout**

Edit `resources/views/components/layouts/app.blade.php` (or the equivalent layout file — find it with `ls resources/views/components/layouts/ resources/views/layouts/ 2>/dev/null`):

Remove any `@auth` / `@guest` blocks, login/register/logout links, and user avatar/dropdown. Leave the shell with a simple nav placeholder — real nav added in Task 4.

- [ ] **Step 7: Run migrate:fresh and tests**

```bash
php artisan migrate:fresh
php artisan test
```

Expected: migrations run clean. Tests: the starter-kit auth tests are gone; remaining tests (if any) pass. A failing `ExampleTest` is fine — it stays.

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "chore: strip auth scaffolding from starter kit"
```

---

## Task 3: Create Link model and migration

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_links_table.php`
- Create: `app/Models/Link.php`
- Create: `database/factories/LinkFactory.php`
- Create: `tests/Feature/LinkModelTest.php`

- [ ] **Step 1: Generate migration**

```bash
php artisan make:migration create_links_table
```

- [ ] **Step 2: Write the migration**

Edit the generated file `database/migrations/*_create_links_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->text('original_url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
```

- [ ] **Step 3: Generate model and factory**

```bash
php artisan make:model Link -f
```

- [ ] **Step 4: Write the model**

Edit `app/Models/Link.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'original_url'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
```

- [ ] **Step 5: Write the factory**

Edit `database/factories/LinkFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => Str::random(6),
            'original_url' => fake()->url(),
        ];
    }
}
```

- [ ] **Step 6: Write the failing model test**

Create `tests/Feature/LinkModelTest.php`:

```php
<?php

use App\Models\Link;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('link uses slug as route key', function () {
    $link = new Link();
    expect($link->getRouteKeyName())->toBe('slug');
});

test('link can be created with fillable attributes', function () {
    $link = Link::create([
        'slug' => 'abc123',
        'original_url' => 'https://example.com',
    ]);

    expect($link->slug)->toBe('abc123');
    expect($link->original_url)->toBe('https://example.com');
});

test('link factory produces valid links', function () {
    $link = Link::factory()->create();
    expect($link->slug)->toBeString();
    expect($link->original_url)->toBeString();
});

test('slug must be unique', function () {
    Link::factory()->create(['slug' => 'taken1']);
    expect(fn () => Link::factory()->create(['slug' => 'taken1']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
```

- [ ] **Step 7: Configure Pest for in-memory SQLite**

Edit `phpunit.xml` — verify this is inside `<php>`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

If not present, add them.

- [ ] **Step 8: Run tests**

```bash
php artisan test --filter=LinkModelTest
```

Expected: all four tests pass.

- [ ] **Step 9: Commit**

```bash
git add -A
git commit -m "feat: add Link model, migration, factory"
```

---

## Task 4: Layout, navigation, and placeholder routes

**Files:**
- Modify: `resources/views/components/layouts/app.blade.php` (or equivalent)
- Modify: `routes/web.php`
- Create: `resources/views/pages/about.blade.php`

- [ ] **Step 1: Identify the main layout file**

```bash
ls resources/views/components/layouts/ 2>/dev/null
ls resources/views/layouts/ 2>/dev/null
```

The Livewire starter kit typically places it at `resources/views/components/layouts/app.blade.php`. Use whichever path exists.

- [ ] **Step 2: Update layout with nav**

Replace the nav section with:

```blade
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
```

Ensure the body has `bg-slate-50 text-slate-900 min-h-screen` classes.

Add a flash-message partial somewhere inside the main content container:

```blade
@if (session('flash'))
    <div class="max-w-4xl mx-auto px-4 mt-4">
        <div class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 rounded">
            {{ session('flash') }}
        </div>
    </div>
@endif
```

- [ ] **Step 3: Create placeholder routes**

Edit `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.shorten-placeholder')->name('shorten');
Route::view('/history', 'pages.history-placeholder')->name('history');
Route::view('/about', 'pages.about')->name('about');
```

- [ ] **Step 4: Create placeholder views**

Create `resources/views/pages/shorten-placeholder.blade.php`:

```blade
<x-layouts.app>
    <div class="max-w-4xl mx-auto p-4">Shorten page placeholder.</div>
</x-layouts.app>
```

Create `resources/views/pages/history-placeholder.blade.php`:

```blade
<x-layouts.app>
    <div class="max-w-4xl mx-auto p-4">History page placeholder.</div>
</x-layouts.app>
```

Create `resources/views/pages/about.blade.php`:

```blade
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
```

- [ ] **Step 5: Write a nav smoke test**

Create `tests/Feature/NavigationTest.php`:

```php
<?php

test('shorten page loads', function () {
    $this->get('/')->assertOk()->assertSee('Shorten');
});

test('history page loads', function () {
    $this->get('/history')->assertOk()->assertSee('History');
});

test('about page loads', function () {
    $this->get('/about')->assertOk()->assertSee('About Shorty');
});
```

- [ ] **Step 6: Run tests**

```bash
php artisan test --filter=NavigationTest
```

Expected: all three pass.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: add layout nav and placeholder pages"
```

---

## Task 5: StoreLinkRequest validation

**Files:**
- Create: `app/Http/Requests/StoreLinkRequest.php`
- Create: `tests/Feature/StoreLinkRequestTest.php`

- [ ] **Step 1: Generate the form request**

```bash
php artisan make:request StoreLinkRequest
```

- [ ] **Step 2: Write failing test**

Create `tests/Feature/StoreLinkRequestTest.php`:

```php
<?php

use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use Illuminate\Support\Facades\Validator;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function validateLink(array $data): \Illuminate\Contracts\Validation\Validator
{
    $rules = (new StoreLinkRequest())->rules();
    return Validator::make($data, $rules);
}

test('valid url without slug passes', function () {
    expect(validateLink(['url' => 'https://example.com'])->passes())->toBeTrue();
});

test('valid url with slug passes', function () {
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'mylink'])->passes())->toBeTrue();
});

test('missing url fails', function () {
    expect(validateLink([])->passes())->toBeFalse();
});

test('invalid url fails', function () {
    expect(validateLink(['url' => 'not-a-url'])->passes())->toBeFalse();
});

test('non-http url fails', function () {
    expect(validateLink(['url' => 'ftp://example.com'])->passes())->toBeFalse();
});

test('slug too short fails', function () {
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'abc'])->passes())->toBeFalse();
});

test('slug with bad chars fails', function () {
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'bad slug!'])->passes())->toBeFalse();
});

test('slug collision fails', function () {
    Link::factory()->create(['slug' => 'taken1']);
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'taken1'])->passes())->toBeFalse();
});
```

- [ ] **Step 3: Run tests to verify they fail**

```bash
php artisan test --filter=StoreLinkRequestTest
```

Expected: FAIL — rules are default/empty.

- [ ] **Step 4: Implement the form request**

Edit `app/Http/Requests/StoreLinkRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url:http,https', 'max:2048'],
            'slug' => ['nullable', 'alpha_dash', 'min:6', 'max:32', 'unique:links,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'slug taken, try another',
        ];
    }
}
```

- [ ] **Step 5: Run tests to verify they pass**

```bash
php artisan test --filter=StoreLinkRequestTest
```

Expected: all 8 tests pass.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: add StoreLinkRequest validation rules"
```

---

## Task 6: Shorten Volt component (create form)

**Files:**
- Create: `resources/views/livewire/shorten.blade.php`
- Modify: `routes/web.php`
- Delete: `resources/views/pages/shorten-placeholder.blade.php`
- Create: `tests/Feature/ShortenComponentTest.php`

- [ ] **Step 1: Write failing component tests**

Create `tests/Feature/ShortenComponentTest.php`:

```php
<?php

use App\Models\Link;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('submitting valid url creates a link and redirects', function () {
    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->call('save')
        ->assertRedirect('/history');

    expect(Link::count())->toBe(1);
    expect(Link::first()->original_url)->toBe('https://example.com');
    expect(Link::first()->slug)->toHaveLength(6);
});

test('submitting url with custom slug uses that slug', function () {
    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->set('slug', 'mylink')
        ->call('save')
        ->assertRedirect('/history');

    expect(Link::where('slug', 'mylink')->exists())->toBeTrue();
});

test('invalid url shows validation error and does not create link', function () {
    Volt::test('shorten')
        ->set('url', 'not-a-url')
        ->call('save')
        ->assertHasErrors(['url']);

    expect(Link::count())->toBe(0);
});

test('slug collision shows validation error', function () {
    Link::factory()->create(['slug' => 'taken1']);

    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->set('slug', 'taken1')
        ->call('save')
        ->assertHasErrors(['slug']);

    expect(Link::count())->toBe(1);
});

test('empty list initially — create sets flash message', function () {
    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->call('save');

    expect(session('flash'))->toContain('Link created');
});
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test --filter=ShortenComponentTest
```

Expected: FAIL — component does not exist.

- [ ] **Step 3: Create the Volt component**

Create `resources/views/livewire/shorten.blade.php`:

```blade
<?php

use App\Models\Link;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $url = '';
    public string $slug = '';

    public function save(): void
    {
        $validated = $this->validate([
            'url' => ['required', 'url:http,https', 'max:2048'],
            'slug' => ['nullable', 'alpha_dash', 'min:6', 'max:32', 'unique:links,slug'],
        ], [
            'slug.unique' => 'slug taken, try another',
        ]);

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
```

- [ ] **Step 4: Wire the route**

Edit `routes/web.php`, replace the `/` placeholder with:

```php
use Livewire\Volt\Volt;

Volt::route('/', 'shorten')->name('shorten');
```

Keep the other route definitions as-is.

- [ ] **Step 5: Remove the old placeholder**

```bash
rm resources/views/pages/shorten-placeholder.blade.php
```

- [ ] **Step 6: Run tests**

```bash
php artisan test --filter=ShortenComponentTest
```

Expected: all 5 tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: add shorten Volt component with create form"
```

---

## Task 7: History Volt component (list + delete)

**Files:**
- Create: `resources/views/livewire/history.blade.php`
- Modify: `routes/web.php`
- Delete: `resources/views/pages/history-placeholder.blade.php`
- Create: `tests/Feature/HistoryComponentTest.php`

- [ ] **Step 1: Write failing component tests**

Create `tests/Feature/HistoryComponentTest.php`:

```php
<?php

use App\Models\Link;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('history renders empty state when no links', function () {
    Volt::test('history')
        ->assertSee('No links yet');
});

test('history lists all links newest first', function () {
    $old = Link::factory()->create(['slug' => 'oldone', 'created_at' => now()->subHour()]);
    $new = Link::factory()->create(['slug' => 'newone', 'created_at' => now()]);

    $component = Volt::test('history');
    $component->assertSee('oldone');
    $component->assertSee('newone');
    $component->assertSeeInOrder(['newone', 'oldone']);
});

test('delete removes the link', function () {
    $link = Link::factory()->create(['slug' => 'tobedel']);

    Volt::test('history')
        ->call('delete', $link->id);

    expect(Link::find($link->id))->toBeNull();
});
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test --filter=HistoryComponentTest
```

Expected: FAIL — component does not exist.

- [ ] **Step 3: Create the Volt component**

Create `resources/views/livewire/history.blade.php`:

```blade
<?php

use App\Models\Link;
use Livewire\Volt\Component;

new class extends Component {
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
```

- [ ] **Step 4: Wire the route**

Edit `routes/web.php`, replace the `/history` placeholder with:

```php
Volt::route('/history', 'history')->name('history');
```

- [ ] **Step 5: Remove placeholder**

```bash
rm resources/views/pages/history-placeholder.blade.php
```

- [ ] **Step 6: Run tests**

```bash
php artisan test --filter=HistoryComponentTest
```

Expected: all 3 tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: add history Volt component with list and delete"
```

---

## Task 8: Redirect route

**Files:**
- Create: `app/Http/Controllers/RedirectController.php`
- Create: `resources/views/errors/link-not-found.blade.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/RedirectTest.php`

- [ ] **Step 1: Write failing tests**

Create `tests/Feature/RedirectTest.php`:

```php
<?php

use App\Models\Link;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('existing slug redirects 302 to original url', function () {
    Link::factory()->create([
        'slug' => 'abc123',
        'original_url' => 'https://example.com/page',
    ]);

    $response = $this->get('/r/abc123');
    $response->assertStatus(302);
    $response->assertRedirect('https://example.com/page');
});

test('missing slug returns 404 with link-not-found view', function () {
    $this->get('/r/nosuch')
        ->assertStatus(404)
        ->assertSee('Link not found');
});
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test --filter=RedirectTest
```

Expected: FAIL — route does not exist.

- [ ] **Step 3: Create the controller**

```bash
php artisan make:controller RedirectController
```

Edit `app/Http/Controllers/RedirectController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Link;

class RedirectController extends Controller
{
    public function __invoke(string $slug)
    {
        $link = Link::where('slug', $slug)->first();

        if (! $link) {
            abort(404);
        }

        return redirect()->away($link->original_url, 302);
    }
}
```

- [ ] **Step 4: Create the 404 view**

Create `resources/views/errors/404.blade.php`:

```blade
<x-layouts.app>
    <div class="max-w-2xl mx-auto p-8 text-center">
        <h1 class="font-bold text-2xl text-slate-900 mb-2">Link not found</h1>
        <p class="text-slate-500">This short link doesn't exist or has been deleted.</p>
        <a href="{{ route('shorten') }}" wire:navigate
           class="inline-block mt-4 text-teal-600 underline">Create a new one</a>
    </div>
</x-layouts.app>
```

- [ ] **Step 5: Wire the route**

Edit `routes/web.php`:

```php
use App\Http\Controllers\RedirectController;

Route::get('/r/{slug}', RedirectController::class)->name('redirect');
```

- [ ] **Step 6: Run tests**

```bash
php artisan test --filter=RedirectTest
```

Expected: both tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: add slug redirect route with 404 page"
```

---

## Task 9: Rate limiting on create

**Files:**
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/RateLimitTest.php`

- [ ] **Step 1: Write failing test**

Create `tests/Feature/RateLimitTest.php`:

```php
<?php

use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('rate limit kicks in after 20 creates per minute', function () {
    for ($i = 0; $i < 20; $i++) {
        Volt::test('shorten')
            ->set('url', "https://example.com/{$i}")
            ->call('save');
    }

    Volt::test('shorten')
        ->set('url', 'https://example.com/over')
        ->call('save')
        ->assertHasErrors('url');
});
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test --filter=RateLimitTest
```

Expected: FAIL — no rate limit enforced.

- [ ] **Step 3: Register the named limiter**

Edit `app/Providers/AppServiceProvider.php`. In the `boot()` method, add:

```php
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter as RateLimiterFacade;

// inside boot():
RateLimiterFacade::for('create-link', function (Request $request) {
    return Limit::perMinute(20)->by($request->ip());
});
```

Ensure the use statements are added at the top of the file.

- [ ] **Step 4: Enforce rate limit in the component**

Edit `resources/views/livewire/shorten.blade.php`. In the `save()` method, before `$this->validate(...)`, add:

```php
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

// at the top of save():
$key = 'create-link:' . request()->ip();
if (RateLimiter::tooManyAttempts($key, 20)) {
    $seconds = RateLimiter::availableIn($key);
    throw ValidationException::withMessages([
        'url' => "Slow down! Try again in {$seconds} seconds.",
    ]);
}
RateLimiter::hit($key, 60);
```

Add the two `use` statements at the top of the file's `<?php` block.

- [ ] **Step 5: Run rate limit tests**

```bash
php artisan test --filter=RateLimitTest
```

Expected: pass.

- [ ] **Step 6: Run all tests to confirm no regressions**

```bash
php artisan test
```

Expected: entire suite passes.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: rate limit link creation to 20 per minute per IP"
```

---

## Task 10: Laravel Cloud deployment config

**Files:**
- Create: `.laravelcloud/config.yml` (or confirm default is sufficient)
- Modify: `.env.example`

- [ ] **Step 1: Update .env.example**

Edit `.env.example` so the database section reads:

```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

- [ ] **Step 2: Verify Laravel Cloud picks up defaults**

Laravel Cloud auto-detects Laravel apps. No explicit config file is required for a basic SQLite + Livewire app. Document this in a `README.md` note at project root:

If `README.md` exists, append:

```markdown
## Deployment

Deployed on Laravel Cloud. SQLite is stored as `database/database.sqlite`. Push to main to deploy.
```

If `README.md` does not exist, skip — deferring to Cloud's defaults is acceptable.

- [ ] **Step 3: Run full test suite one last time**

```bash
php artisan test
```

Expected: entire suite passes.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "chore: document Laravel Cloud deployment and SQLite defaults"
```

---

## Final Verification

- [ ] Run `php artisan test` — all tests pass
- [ ] Run `php artisan serve` and manually verify:
    - `/` renders the form
    - Submitting a URL redirects to `/history` with flash message
    - `/history` lists the link, Copy button works, Delete prompts and removes
    - `/about` renders correctly
    - `/r/<slug>` redirects
    - `/r/nosuch` shows the 404 page
- [ ] Review `git log` — 10 clean commits, one per task
