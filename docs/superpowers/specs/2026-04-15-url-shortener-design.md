# URL Shortener — Design Spec

## 1. Concept

A minimal public link shortener. No auth, no analytics, no accounts. Anyone who opens the site can create and manage links. Three pages plus a redirect route.

## 2. Stack

- **Framework:** Laravel 11
- **Starter kit:** Livewire (Livewire 3 + Volt + Tailwind + Alpine)
- **Database:** SQLite (default, single file)
- **Hosting:** Laravel Cloud
- **Testing:** Pest

Auth scaffolding from the starter kit is removed (see §8). Layout, nav shell, and asset pipeline are kept.

## 3. Design Language

**Colors:**
- Primary: teal-500 (`#14b8a6`) — buttons, links
- Background: slate-50 (`#f8fafc`)
- Surface: white (`#ffffff`)
- Border: slate-200 (`#e2e8f0`)
- Text: slate-900 (`#0f172a`)
- Muted: slate-500 (`#64748b`)
- Error: red-500 (`#ef4444`)

**Typography:**
- Headings: `font-bold text-2xl`
- Body: `text-base`
- Slugs: `font-mono text-teal-600`

**Motion:**
- Copy feedback: icon swap, 1.5s reset (Alpine)
- Form submit: spinner on button (Livewire `wire:loading`)
- List item delete: fade-out 200ms

## 4. Routes & Pages

| Route | Method | Purpose |
|-------|--------|---------|
| `/` | GET | Create form (Volt page) |
| `/history` | GET | List + delete links (Volt page) |
| `/about` | GET | Static about page (Blade view) |
| `/r/{link}` | GET | 302 redirect to `original_url`, or 404 |

**Navigation:** Top bar in `layouts/app.blade.php` with logo + three links (`Shorten` / `History` / `About`), all using `wire:navigate` for SPA-smooth transitions.

### 4.1 `/` — Create

Volt component with:
- URL field (required)
- Optional custom slug field
- Submit button

On submit:
- Success → `redirect('/history')` with flash message (`Link created: <slug>`)
- Validation error → inline messages, stay on page

### 4.2 `/history` — List & Manage

Volt component listing all links, newest first. Each row:

```
slug | original URL (truncated) | created date | [Copy] [Delete]
```

- **Copy:** Alpine snippet — writes `https://<host>/r/<slug>` to clipboard, swaps icon to checkmark for 1.5s.
- **Delete:** `wire:confirm` dialog → delete row → fade-out 200ms.
- **Empty state:** "No links yet — create one above!" with link to `/`.

### 4.3 `/about`

Plain Blade view (no Livewire needed):
- Paragraph: what the tool is
- "How it works" — 3 steps: paste → shorten → share
- Tech stack line
- Source link

### 4.4 `/r/{link}` — Redirect

Controller action (not Livewire). Route model binding resolves `Link` by `slug` via `getRouteKeyName()`. 302 redirect to `original_url`, or 404 view ("Link not found") if missing.

## 5. Data Model

**Migration (`links` table):**
```php
$table->id();                     // bigint auto-increment PK
$table->string('slug')->unique(); // indexed, used in URLs
$table->text('original_url');
$table->timestamps();             // created_at + updated_at
```

**Model:** `App\Models\Link`
- `$fillable = ['slug', 'original_url']`
- `getRouteKeyName(): string` returns `'slug'` for implicit route binding

## 6. Validation

Form Request `StoreLinkRequest`:

| Field | Rules |
|-------|-------|
| `url` | `required`, `url:http,https`, `max:2048` |
| `slug` | `nullable`, `alpha_dash`, `min:6`, `max:32`, `unique:links,slug` |

**Random slug generation:** when `slug` is omitted, generate via `Str::random(6)`. Retry on collision up to 5 times. Exhaustion → 500 error (astronomically unlikely at this scale).

## 7. Rate Limiting

Named limiter registered in `AppServiceProvider::boot()`:

```php
RateLimiter::for('create-link', fn (Request $r) =>
    Limit::perMinute(20)->by($r->ip())
);
```

Applied via `throttle:create-link` middleware on the create action. 429 response → UI shows "Slow down! Try again in X seconds" using `Retry-After` header.

## 8. Starter Kit Cleanup

After `laravel new url-short --livewire`:

**Delete:**
- `routes/auth.php` include from `routes/web.php`
- Auth Volt components: `login`, `register`, `forgot-password`, `reset-password`, `verify-email`, `confirm-password`, `settings/*`
- `users` migration
- `auth` / `verified` middleware usage

**Keep:**
- `layouts/app.blade.php` and nav component structure
- Tailwind config + asset pipeline
- Flash message partials
- Volt + Livewire + Alpine wiring

## 9. Edge Cases

| Case | Handling |
|------|----------|
| Invalid URL submitted | Inline red error under URL field |
| Slug too short / bad chars | Inline red error under slug field |
| Slug collision | Inline amber error: "slug taken, try another" |
| Slug not found on redirect | 404 view: "Link not found" |
| Rate limit exceeded | 429 + "Slow down! Try again in X seconds" |
| Empty list on `/history` | "No links yet — create one above!" |
| Random slug exhausts 5 retries | 500 generic error |

## 10. Testing

Pest feature tests using `RefreshDatabase` + in-memory SQLite.

- **Create, no slug:** valid URL → persisted with random 6-char slug, redirects to `/history` with flash.
- **Create, custom slug:** valid URL + valid slug → persisted with that slug.
- **Validation errors:** invalid URL → `assertHasErrors('url')`; bad slug → `assertHasErrors('slug')`.
- **Slug collision:** pre-seed link, submit same slug → `assertHasErrors('slug')` (unique rule).
- **History listing:** seed 3 links → all render newest-first.
- **Delete:** seed link, invoke delete action → row removed from DB.
- **Redirect:** `/r/{slug}` existing → 302 to `original_url`; missing → 404.
- **Rate limit:** 21 requests in one minute from same IP → 21st returns 429.

Livewire components tested via `Livewire::test(...)`. Redirect controller via `$this->get(...)`.

## 11. Out of Scope

- Analytics, click tracking, UTM params
- QR codes
- Link expiry
- User accounts / auth
- Geolocation
- Link editing
