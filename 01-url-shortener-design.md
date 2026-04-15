# URL Shortener — Design Spec

## 1. Concept & Vision

The simplest possible link shortener. Paste a URL, get a short slug, share it. Single-page app — one `/` page that holds the form and the list. No auth, no analytics, no accounts. Anyone who opens the site can create and manage links. No admin path — list is visible to everyone.

## 2. Design Language

**Colors:**
- Primary: teal-500 (#14b8a6) — buttons, links
- Background: slate-50 (#f8fafc)
- Surface: white (#ffffff)
- Border: slate-200 (#e2e8f0)
- Text: slate-900 (#0f172a)
- Muted: slate-500 (#64748b)
- Error: red-500 (#ef4444)

**Typography:**
- Headings: `font-bold text-2xl`
- Body: `text-base`
- Slugs: `font-mono text-teal-600`

**Motion:**
- Copy feedback: icon swap, 1.5s reset
- Form submit: subtle spinner on button
- List item delete: fade-out 200ms

## 3. Layout & Structure

Single page: `/`

```
Header: logo + tagline
Main:   URL input form (full width) + optional slug input
Below:  list of created links
Footer: minimal
```

Mobile-first, single column. Cards stack vertically.

## 4. Features & Interactions

### Create Link
- Input: paste/type long URL (required)
- Input: custom slug (optional, defaults to random 6-char alphanumeric)
- Submit → POST /api/links
- Success → append to list, show slug with copy button
- Error (invalid URL) → inline red message under input
- Error (slug taken) → inline amber message "slug taken, try another"

### List Links
- Shows all links: slug | original URL (truncated) | created date | [Copy] [Delete]
- Copy → clipboard, icon swaps to checkmark 1.5s
- Delete → confirm dialog → DELETE /api/links/[slug], remove from list

### Redirect
- GET `/r/[slug]` → 302 redirect to originalUrl
- Not found → 404 page "Link not found"
- No UI shown — browser redirects immediately

### Rate Limiting
- 20 creates/min per IP
- 429 response → UI shows "Slow down! Try again in X seconds"

## 5. Data Model

**Link**
```ts
{
  slug: string,        // primary key, URL-safe, 6-32 chars
  originalUrl: string, // required, valid URL
  createdAt: string,   // ISO timestamp
}
```

## 6. API

| Method | Path | Body | Response |
|--------|------|------|----------|
| POST | `/api/links` | `{ url: string, slug?: string }` | `Link` or `400/409` |
| GET | `/api/links` | — | `Link[]` |
| DELETE | `/api/links/[slug]` | — | `204` or `404` |
| GET | `/r/[slug]` | — | `302` to originalUrl or `404` |

## 7. Edge Cases

| Case | Handling |
|------|----------|
| Invalid URL submitted | 400, inline error message |
| Slug collision | 409, inline "slug taken" message |
| Slug not found on redirect | 404 page with "Link not found" message |
| Rate limit exceeded | 429 with Retry-After, UI throttles submit button |
| Empty list | "No links yet — create one above!" |

## 8. Out of Scope

- Analytics, click tracking, UTM params
- QR codes
- Link expiry
- Custom admin path
- User accounts / auth
- Geolocation
- Link editing
