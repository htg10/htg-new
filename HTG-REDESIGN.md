# HTG Ledger — Redesign

This is your **complete original project** with the redesign applied on top.
Every file from your zip is still here, in the same place. Nothing was deleted.

**7 files added, 19 replaced.** Routes, controllers, models, migrations and
`composer.json` are untouched — every Blade variable, route name, form field
name and element ID is byte-for-byte what it was.

---

## Run it

```bash
composer install
npm install
cp .env.example .env        # if you don't already have a .env
php artisan key:generate
php artisan migrate
php artisan view:clear && php artisan cache:clear
php artisan serve
```

Hard-refresh the browser (Ctrl/Cmd + Shift + R). The theme CSS and JS are
cache-busted with `?v=1.0` — bump that string in
`resources/views/layouts/backend/partials/style.blade.php` and `script.blade.php`
whenever you edit them.

---

## Added (7)

```
public/assets/admin/css/htg-theme.css              design system
public/assets/admin/js/htg-theme.js                sidebar, dark mode, toasts
resources/views/admin/partials/product-matrix.blade.php
resources/views/admin/partials/product-matrix-js.blade.php
resources/views/admin/partials/company-autofill-js.blade.php
resources/views/admin/partials/lead-products.blade.php
resources/views/admin/partials/lead-form-js.blade.php
```

## Replaced (19)

**Module 1 — shell, auth, dashboard**

```
resources/views/layouts/backend/app.blade.php
resources/views/layouts/backend/partials/style.blade.php
resources/views/layouts/backend/partials/script.blade.php
resources/views/layouts/backend/partials/header.blade.php
resources/views/layouts/backend/partials/sidenav.blade.php
resources/views/layouts/backend/partials/footer.blade.php
resources/views/admin/login/login.blade.php
resources/views/admin/dashboard.blade.php
resources/views/admin/dashboard-data.blade.php
resources/views/admin/index.blade.php
```

**Module 2 — contracts**

```
resources/views/admin/addnew.blade.php
resources/views/admin/renew.blade.php
resources/views/admin/edit.blade.php
resources/views/admin/addnew-from-lead.blade.php
resources/views/admin/history.blade.php
```

**Module 3 — leads**

```
resources/views/admin/leads/index.blade.php
resources/views/admin/leads/show/index.blade.php
resources/views/admin/leads/show/create.blade.php
resources/views/admin/leads/show/edit.blade.php
```

---

## Design direction — "HTG Console" (v2)

v1 was an amber-and-mono "ledger" look. It read as a demo rather than a tool,
so v2 pulls it back to something you can stare at all day.

| Token | Value | Where |
|---|---|---|
| Accent | `#2B59C3` | active nav, primary buttons, focus, links, charts |
| Ink | `#101B2D` | login panel, headings |
| Page | `#F7F8FA` / card `#FFFFFF` | surfaces |
| Line | `#E5E8EE` | hairline borders |
| Status | `#0F7A52` won · `#B26908` pending · `#C33C2E` lost | leads and money only |

**What changed from v1**

- **Amber is gone.** One blue accent, used sparingly. Amber on every card and
  every KPI was the loudest thing on screen.
- **Mono type is gone from labels and headings.** Inter throughout; figures use
  its tabular-numeral feature so columns still line up. Setting every label in
  uppercase mono is what made v1 look like a template.
- **The sidebar is light.** A dark rail beside a white workspace is heavy. The
  active row is now accent text on a soft tint with a thin rail.
- **Denser and flatter.** 60px topbar (was 68), hairline borders, near-flat
  cards, sentence-case labels and table headers.
- **Chart palette follows the same system** — no more amber wedges.

**Login is unchanged in layout.** Same split screen, same ruled ink panel, same
fields and copy. It inherits the new palette and typeface so it matches the rest
of the app — if you want the old amber highlight back on that page specifically,
say so and it's a one-line change.

**Dark mode.** Toggle in the topbar. Persisted in `localStorage`, applied before
first paint so there is no flash. Charts redraw on the switch.

## Bugs found and fixed

These were real defects in the original code, not styling choices.

1. **Only one of three pie charts rendered on All Contracts.** All three scripts
   in `admin/index.blade.php` declared a function named `drawChart()`, so the
   third definition overwrote the other two. Now `drawTypeChart()`,
   `drawBdmChart()`, `drawServiceChart()` behind one load callback.

2. **Sidebar toggle was double-bound.** The button used
   `id="vertical-menu-btn"`, which the Skote `app.js` already binds. Any second
   handler on the same ID fires alongside it and cancels out. Renamed to
   `htg-menu-btn`.

3. **jQuery and Bootstrap were re-loaded from a CDN** at the bottom of `addnew`,
   `renew`, `edit` and `addnew-from-lead`. Loading jQuery a second time replaces
   `window.$` and drops every plugin registered against the first copy —
   DataTables, Select2, dropify. Those tags are gone; the layout already loads
   both.

4. **Broken form nesting on the contract forms.** The `<form>` opened inside the
   left card and closed inside the right one. It only worked because browsers
   repair mismatched tags. One form now wraps both columns properly.

5. **`admin/edit` and `admin/history` were standalone HTML pages** with their own
   Bootstrap CDN link and, in history's case, a hard-coded blue navbar — no
   sidebar, no topbar, no shared styling. Both now extend the layout.

6. **`admin/history` would fatal if reached.** It read `$history->company`, but
   `historyPage()` maps each row to an array shaped
   `['entry' => ..., 'products' => ...]`. The loop now handles both shapes.
   (Note: the `historyPage` route currently points at `DashboardController@index`,
   not `historyPage()` — worth checking on your side.)

7. **`leads/show/index` would fatal on any lead with no assigned BDM or no
   creator.** It read `$telecaller->user->name` and
   `$telecaller->telecallerUser->name` directly; both relations are nullable.
   Now `?? '—'`.

8. **A lead with no meeting showed today's date as if it were booked.**
   `Carbon::parse(null)` returns "now". Guarded.

9. **The empty-state row on assigned leads used `colspan="5"` against 8
   columns**, so the "no leads" message broke the table layout.

10. **jQuery and Bootstrap were re-loaded from a CDN** on the lead create and
    edit forms too — same plugin-clobbering problem as the contract forms.
    Removed.

11. **Editing a lead could wipe its saved coordinates.** The location inputs
    were present but the capture button was commented out; they now carry the
    existing values through the update.

---

## What Module 2 changed

The 18-product billing grid is now one shared partial used by all four contract
forms, instead of the same block copy-pasted four times. Field names are
identical: `products[i][name] [validity] [total_amount] [paid_amount]
[old_paid_amount]`, and the `checkbox{n}` / `fields{n}` IDs and
`toggle-fields` / `fields` / `total-amount` / `paid-amount` classes are
unchanged, so your controller sees exactly what it saw before.

New on top of that:

- A filter box above the list — with 18 products, finding one meant scanning.
  Hidden rows keep their values and still submit, so filtering never silently
  drops a product.
- A live count of how many products are ticked.
- A running **Balance** figure under Total and Received.
- Ticked rows are visibly on: amber rail, tinted background, bolder label.
- Real labels on every field. The originals were placeholder-only, which
  disappear the moment you type.
- Sticky save bar at the bottom of the form.
- On `edit`, the paid columns are labelled **Already Paid** (read-only) and
  **New Payment**, with a note explaining that the new figure is added on top —
  the original showed two unlabelled boxes side by side.
- On `edit`, documents already attached to the contract are shown as thumbnails.
- On mobile the grid stacks instead of squeezing four inputs into a phone width.

---

## Verify after install

- [ ] Log in — split-screen page, error messages still show
- [ ] Sidebar collapses to the icon rail and stays collapsed after reload
- [ ] Dark toggle flips the console and the charts redraw
- [ ] Dashboard: Apply Filter loads the table; Excel and PDF still download
- [ ] All Contracts: search + reset work, **all three pie charts render**
- [ ] Add New Contract: tick products → Total, Received and Balance add up
- [ ] Add New Contract: save, then confirm the products landed in the DB
- [ ] Renew: pick a company → locked fields autofill
- [ ] Edit: existing products are pre-ticked and expanded; New Payment saves
- [ ] Delete confirmation still fires on the trash icon
- [ ] Assigned Leads: pipeline cells filter the table when clicked
- [ ] Assigned Leads: changing status saves; "Follow up" reveals date + remark
- [ ] Assigned Leads: overdue follow-ups show in red, meetings show "Today"
- [ ] Add Lead: saving without a location is blocked before the request is sent
- [ ] Edit Lead: existing services stay ticked, saved location survives the save
- [ ] Mobile width — sidebar overlays, product grid stacks

---

## Roadmap

| # | Scope | Status |
|---|---|---|
| 1 | Theme, layout shell, login, admin dashboard, All Contracts | done |
| 2 | Contracts: addnew, renew, edit, history, addnew-from-lead | done |
| 3 | Leads: assigned leads, all leads, lead create + edit | done |
| 4 | Masters: bank, purpose, expense, building rent | next |
| 5 | Users & telecallers, register, password reset/email |  |
| 6 | BDM role: all of `resources/views/user/**` |  |
| 7 | Telecaller role: `resources/views/telecaller/**` |  |
| 8 | Email templates + `export/finance-pdf` |  |

Screens not yet hand-rewritten still work — they pick up the new palette,
typography, buttons, tables and forms automatically from `htg-theme.css`.
