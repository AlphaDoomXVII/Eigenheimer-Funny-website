# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Funny website for B&B Eigenheimer (https://bbeigenheimer.nl/). Plain PHP MVC app, no framework/build tools — allowed stack is PHP, jQuery, Bootstrap, Font Awesome only. Structure and CRUD/MVC conventions are ported from https://github.com/hetgameboekje/ticketsystemVHE.

The goal is two independently deployable apps sharing one `app/Core` + `app/Shared` layer: a public **webapp** (rooms, food ordering, contact) and a staff **intranet** (management). See README.md's `## Meervoudig plan (fasering)` for the phase-by-phase roadmap (Fase 1 done, Fase 2 = kamerbeheer, Fase 3 = eten per dagdeel, Fase 4 = rollen/auth, Fase 5 = intranet split, Fase 6 = polish). Check the README before starting work on a new phase — it tracks what's already built vs. still open.

Comments, commit-style docs, and UI strings in this repo are Dutch; match that.

## Running locally

No composer/npm — nothing to install. Requires PHP with the `pdo_mysql` extension and a MySQL/MariaDB database.

```
php -S localhost:8000 -t public public/router.php
```

`public/router.php` serves existing files directly and routes everything else through `public/index.php` (this emulates the `.htaccess` rewrite used under Apache). Config reads `DB_*` from a `.env` file in the project root (copy `.env.example` → `.env`); if `.env` is absent, `config/config.php` falls back to hardcoded local defaults.

Load `database/schema.sql` to get the `features` table (and its seed rows) that the rights system reads. `schema.sql` is generated from `database/xml/*.xml` (one XML file per table, ported from ticketsystemVHE's `database/xml` + `app/Core/SchemaParser.php` pattern) — after editing a table's XML, run `php database/parse.php` to regenerate `schema.sql`; don't hand-edit `schema.sql` itself. See `database/xml/README.md` for the XML format.

There are no automated tests or a lint/build step configured. Sanity-check changes with `php -l <file>` and by exercising the route manually (e.g. the basket add/remove/CSRF-reject flow noted in the README's Fase 1 section).

## Architecture

Everything routes through a single front controller, `public/index.php`, which requires `app/bootstrap.php` then registers routes on `App\Core\Router` and dispatches. There is no `?controller=&action=` query-string dispatch — routes are explicit `$router->get('/path', [Controller::class, 'method'])` calls with `{id}` (numeric) / `{uuid}` (free-text) placeholders.

`app/bootstrap.php` does all cross-cutting setup: defines `APP_ROOT`, loads `.env` into `getenv()`, registers a PSR-4-ish autoloader (`App\` → `app/`), and starts the session. It runs once per request, before routing.

Layers, top to bottom:
- **`app/Core/`** — framework-ish primitives, no business logic: `Router` (regex routing + CSRF check on every POST), `Controller` (`render()` renders a view then wraps it in `Views/layouts/app.php`; `redirect()`), `Database` (lazy PDO singleton, config from `config/config.php`), `Model` (base CRUD: `all/find/create/update/delete`, driven by a subclass's `$table`/`$fillable`), `Csrf` (one token per session, not per form), `Uuid`.
- **`app/Modules/<Name>/`** — one module = `Controller` + `Models/` + `Views/<Name>View/`. `Bestellen` (ordering): `BestellenController` handles `index`/`store`/`destroy`, `Models/MenuItemModel` extends `Core\Model` (table `order_food`, fails safe to `[]` if the table/DB is missing), `Models/BasketModel` is session-only (`$_SESSION['items']`) and deliberately does *not* extend `Core\Model` since it has no table. `Kamers` (rooms, Fase 2): `KamerController` adds a public `index` (available rooms only) alongside CRUD actions (`beheer`/`create`/`store`/`edit`/`update`/`destroy`/`toggle`) on the same controller — there's no intranet/auth split yet, so admin and public actions currently live side by side gated only by URL, not by login. Follow this Controller+Models+Views split for any new module.
- **`app/Shared/`** — cross-cutting concerns used by multiple modules. Currently `Rechten/Models/FeatureModel`: an on/off feature-flag gate backed by the `features` table. A feature missing from the table is treated as **enabled** (fail-open), so an empty/unmigrated table never blocks the whole site. Controllers gate a feature at the top of an action, e.g. `BestellenController::index()` checks `FeatureModel::isEnabled('bestellen')` and renders an `uitgeschakeld` (disabled) view instead when off. Any new toggleable feature (kamers, etc.) should follow this same pattern and get a row in `database/xml/features.xml`, then re-run `php database/parse.php`.
- **`app/Views/`** — `layouts/app.php` is the page shell (head/navbar/main/footer); `partials/` holds `navbar.php`/`footer.php`. Module views live under the module (`app/Modules/<Name>/Views/<Name>View/`), not here.

CSRF: every POST route is checked in `Router::dispatch()` before matching — a missing/invalid `_csrf` field (or `X-CSRF-Token` header) short-circuits with a 419, so any new POST route/form must include the session's `Csrf::token()` (already injected into layouts as `$csrfToken`).

Not yet built (don't assume they exist): `/webapp` and `/intranet` folder split, an `app/Shared/Auth` module with login/roles, and a `CrudController`/`Table`/pagination layer for admin CRUD screens — these land in later phases per the README.
