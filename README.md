# Eigenheimer-Funny-website

Funny website voor B&amp;B Eigenheimer https://bbeigenheimer.nl/

## Projectomschrijving

Dit project verbetert en breidt de bestaande website van B&B Eigenheimer uit met twee losstaande applicaties:

1. **Webapp** — publiek gerichte site (bestellen, kamers bekijken, contact)
2. **Intranet** — beheeromgeving voor het personeel/de eigenaren

Beide applicaties staan in aparte subfolders, zodat ze onafhankelijk van elkaar draaien en niet op hetzelfde systeem hoeven te staan.

## Doel

De huidige site (https://bbeigenheimer.nl/) biedt geen mogelijkheid om:
- Dynamisch kamers toe te voegen of te beheren
- Eten te bestellen, waarbij per dagdeel (ontbijt / lunch / diner) andere opties beschikbaar zijn

Beide functies moeten volledig **uitgeschakeld kunnen worden via een rechtensysteem**, zodat losse onderdelen (bijv. het bestelsysteem) tijdelijk uitgezet kunnen worden zonder de rest van de site te raken.

## Referentie / basis

Als basis voor de CRUD- en MVC-opzet wordt voortgebouwd op de structuur uit:
https://github.com/hetgameboekje/ticketsystemVHE

Daarin is al veel voorwerk gedaan voor hoe CRUD en MVC toegepast moeten worden binnen dit soort projecten.

## Toegestane talen & libraries

- PHP
- jQuery
- Bootstrap
- Font Awesome (indien nodig)

Geen frameworks/build-tools buiten deze stack, om het project licht en makkelijk te hosten te houden.

## Architectuur

De structuur is overgenomen van [ticketsystemVHE](https://github.com/hetgameboekje/ticketsystemVHE), zodat de core van dit project op dezelfde leest geschoeid is:

```
app/                            -> gedeelde core, door beide apps gebruikt
  bootstrap.php              -> autoloader (PSR-4-achtig, App\ -> app/), sessie, timezone, .env
  Core/                      -> generieke bouwstenen, los van businesslogica
    Router.php               -> routing (GET/POST, {id}/{uuid}-placeholders, CSRF-check op POST)
    Controller.php            -> basis-controller: render() (view + layout), redirect(), requireBeheerder()
    Database.php               -> PDO-singleton
    Model.php                   -> basis-CRUD-model (all/find/create/update/delete)
    Csrf.php                     -> CSRF-token per sessie
    Uuid.php                      -> UUID-generator
    SchemaParser.php               -> zet database/xml/*.xml om naar database/schema.sql
  Modules/
    Bestellen/                 -> één module = Controller + Models/ + Views/, zoals elke module in ticketsystemVHE
      BestellenController.php     -> publieke bestelflow (webapp) + menubeheer (intranet) + checkout()
      Models/
        MenuItemModel.php        -> extends Core\Model, tabel order_food
        BasketModel.php           -> sessie-gebaseerd (geen databasetabel, dus geen Core\Model)
        BestellingModel.php       -> extends Core\Model, tabel bestellingen (geplaatste bestellingen)
      Views/BestellenView/
        index.php, beheer.php, vorm.php, uitgeschakeld.php
    Kamers/                    -> zelfde opzet: publieke lijst (webapp) + CRUD (intranet)
    Dashboard/                 -> alleen intranet: openstaande bestellingen + kamer-/menuoverzicht
      DashboardController.php
      Views/DashboardView/index.php
  Shared/
    Auth/                      -> sessiegedreven login, alleen gebruikt door intranet
      Auth.php, AuthController.php, Models/UserModel.php, Views/AuthView/login.php
    Rechten/
      Models/FeatureModel.php  -> rechtensysteem: aan/uit-schakelbare features
      RechtenController.php     -> instellingenscherm /rechten (alleen intranet)
  Views/
    layouts/app.php              -> paginaskelet (head, navbar, footer); kiest navbar op basis van APP_CONTEXT
    partials/navbar-webapp.php, navbar-intranet.php, footer.php
config/
  config.php                    -> DB-config, leest .env
database/
  xml/                            -> tabel-definities, één XML-bestand per tabel (bron van de waarheid)
    README.md                      -> uitleg XML-formaat
  parse.php                       -> genereert database/schema.sql uit database/xml/*.xml
  schema.sql                     -> gegenereerd bestand, niet handmatig bewerken
webapp/public/                  -> publieke site, eigen webroot (zie Fase 5)
  index.php                       -> front controller: definieert APP_CONTEXT + alleen publieke routes
  router.php                       -> voor php -S (lokaal draaien zonder Apache)
  .htaccess                         -> alles naar index.php, behalve bestaande bestanden
  assets/css/                        -> home.css, navbar.css
intranet/public/                -> beheeromgeving, eigen webroot (zie Fase 5)
  index.php                       -> front controller: definieert APP_CONTEXT + login/dashboard/beheer-routes
  router.php, .htaccess, assets/css/  -> zelfde patroon als webapp/public/
```

**Wat is 1-op-1 overgenomen uit ticketsystemVHE:** de `app/`-map met `bootstrap.php` en een `Core/`-laag (Router, Controller, Database, Model, Csrf), de indeling in `Modules/<Naam>/{Controller, Models/, Views/}`, `Shared/` voor cross-cutting concerns zoals rechten, `Views/layouts` + `Views/partials`, `public/` als enige webroot met een front controller die alle routes registreert en dispatcht (i.p.v. de oude `?controller=&action=`-querystring-aanpak), en het XML-gedreven databaseschema (`database/xml/*.xml` + `app/Core/SchemaParser.php` + `database/parse.php`, zie [database/xml/README.md](database/xml/README.md)).

**Wat is (nog) niet overgenomen:** de grote hoeveelheid kant-en-klare modules van het ticketsysteem zelf (tickets, kennisbank, etc.) — alleen de *core* is overgenomen. Ook ontbreken nog `CrudController`/`Table`/paginering (komt pas van pas zodra Fase 2/3 echte CRUD-schermen toevoegt) en een `Auth`-module met login (Fase 4).

## Analyse huidige codebase

- [public/index.php](public/index.php) — front controller: registreert routes op een `Router` en dispatcht naar de bijpassende controller-actie.
- [app/Modules/Bestellen/BestellenController.php](app/Modules/Bestellen/BestellenController.php) — bestelflow: toont menu + winkelmandje, handelt `store`/`destroy` af, checkt de `bestellen`-feature.
- [app/Modules/Bestellen/Models/BasketModel.php](app/Modules/Bestellen/Models/BasketModel.php) — sessie-gebaseerde winkelmandlogica (voorheen vermengd met databaselogica en view-aanroepen in het oude `model/home.php`).
- [app/Modules/Bestellen/Models/MenuItemModel.php](app/Modules/Bestellen/Models/MenuItemModel.php) — databasequery voor het menu (`order_food`), fail-safe bij ontbrekende DB.

**Nog open (komt terug in latere fases):**
- Geen kamer- of menubeheer (CRUD-modules/-schermen) — alleen een `order_food`-tabel wordt al bevraagd.
- Geen rollen-/logingedreven rechtenscherm, alleen simpele aan/uit-features.
- Nog geen aparte webapp/intranet-scheiding (zie hieronder).

## Projectstructuur (webapp + intranet) ✅

```
/webapp/       -> publieke site: kamers bekijken, eten bestellen, contact
/intranet/     -> beheer: kamers, menu, dagdelen, rechten, dashboard met bestellingenoverzicht
```

Sinds Fase 5 heeft elke applicatie zijn eigen `public/` (front controller, router, .htaccess, assets), en dus een eigen webroot die onafhankelijk gehost kan worden. Beide delen dezelfde `app/Core` en `app/Shared`-laag (Router, Controller, Database, Model, Csrf, Auth, Rechten) — net zoals ticketsystemVHE één `app/`-boom heeft voor al zijn modules. Zie [Architectuur](#architectuur) hierboven en Fase 5 hieronder.

## Meervoudig plan (fasering)

### Fase 1 — Fundament & opschonen ✅
- Sessielogica van het winkelmandje losgetrokken naar [app/Modules/Bestellen/Models/BasketModel.php](app/Modules/Bestellen/Models/BasketModel.php), databasequery voor het menu naar [app/Modules/Bestellen/Models/MenuItemModel.php](app/Modules/Bestellen/Models/MenuItemModel.php) — beide los van elkaar en los van view-rendering (voorheen vermengd in één `model/home.php`)
- Structuur overgenomen van [ticketsystemVHE](https://github.com/hetgameboekje/ticketsystemVHE): `app/bootstrap.php` + `app/Core/{Router,Controller,Database,Model,Csrf,Uuid}.php`, modules als `app/Modules/<Naam>/{Controller, Models/, Views/}`, `app/Shared/` voor rechten, `public/` als enige webroot met front controller — zie [Architectuur](#architectuur) hierboven
- Basisrechtensysteem opgezet: [app/Shared/Rechten/Models/FeatureModel.php](app/Shared/Rechten/Models/FeatureModel.php) + [database/schema.sql](database/schema.sql) (tabel `features`). Ontbreekt een feature in de database, dan staat die standaard aan (fail-open) zodat een lege tabel de site niet blokkeert. De `bestellen`-feature is als eerste gekoppeld in `BestellenController`.
- Databaseschema alsnog XML-gedreven gemaakt, naar het patroon van [ticketsystemVHE](https://github.com/hetgameboekje/ticketsystemVHE/tree/main/database/xml): [app/Core/SchemaParser.php](app/Core/SchemaParser.php) zet [database/xml/*.xml](database/xml/) (één bestand per tabel) om naar [database/schema.sql](database/schema.sql) via `php database/parse.php`. `schema.sql` is nu een gegenereerd bestand — wijzigingen aan het schema gaan voortaan via de XML, niet meer rechtstreeks in `schema.sql`. De live-database-apply/dev-sync-kant van het origineel (`applyToDatabase()`, `DevSync`, de "Database toepassen"-knop) is bewust niet overgenomen: dat hangt aan een Beheer-/login-scherm dat hier pas in Fase 4/5 komt.
- CSRF-bescherming op alle POST-routes toegevoegd ([app/Core/Csrf.php](app/Core/Csrf.php) + [app/Core/Router.php](app/Core/Router.php)) — bestond nog niet in de oude opzet
- Oude root-structuur (`index.php`, `controllers/`, `model/`, `view/`) volledig verwijderd, incl. de dode `controllers/indexStan.php` en de nooit afgemaakte `controllers/login.php`/`controllers/contact.php`-stubs
- Getest: `php -l` op alle bestanden, en een lokale `php -S`-run met de volledige bestel-mand-flow (item toevoegen, verwijderen, CSRF-afwijzing bij ongeldig token)

Nog niet gedaan in Fase 1 (komt terug in latere fases): het splitsen naar `/webapp/` en `/intranet/` subfolders, en een rollen-/logingedreven rechtenscherm (`app/Shared/Auth`, naar het patroon van ticketsystemVHE).

### Fase 2 — Kamerbeheer ✅
- Database-model: tabel `kamers` (naam, beschrijving, prijs, foto (URL), beschikbaarheid) toegevoegd aan [database/schema.sql](database/schema.sql), plus [app/Modules/Kamers/Models/KamerModel.php](app/Modules/Kamers/Models/KamerModel.php) (extends `Core\Model`, fail-safe naar `[]` als de tabel/DB ontbreekt, net als `MenuItemModel`)
- Nieuwe module `Kamers` naar het bestaande Controller+Models+Views-patroon: [app/Modules/Kamers/KamerController.php](app/Modules/Kamers/KamerController.php) met `index` (publieke lijst van beschikbare kamers), `beheer` (lijst van alle kamers) en CRUD-acties (`create`/`store`, `edit`/`update`, `destroy`, `toggle` voor activeren/deactiveren)
- Weergave van beschikbare kamers in de webapp op `/kamers` ([app/Modules/Kamers/Views/KamerView/index.php](app/Modules/Kamers/Views/KamerView/index.php)); beheerscherm op `/kamers/beheer` met een gedeeld formulier ([vorm.php](app/Modules/Kamers/Views/KamerView/vorm.php)) voor toevoegen/bewerken
- Rechten-toggle: `kamers`-feature (stond al in `features`-tabel sinds Fase 1) bepaalt of `/kamers` de lijst of de `uitgeschakeld`-view toont — kamerbeheer is dus volledig uit te zetten via het rechtensysteem, net als bestellen
- Bugfix in [app/Core/Controller.php](app/Core/Controller.php): `render()` zette `$csrfToken` pas ná het renderen van de view, waardoor elk formulier dat `$csrfToken` gebruikt (ook de bestaande bestel-mand-forms) een lege/undefined token verstuurde en op elke POST een 419 kreeg. Token wordt nu vóór het renderen van de view gezet.
- Getest: `php -l` op alle nieuwe/gewijzigde bestanden, en een lokale `php -S`-run van `/`, `/kamers`, `/kamers/beheer` en `/kamers/nieuw` (inclusief check dat het CSRF-veld nu een echte token bevat). Geen lokale MySQL beschikbaar in deze omgeving, dus de databasegedreven CRUD-flow (aanmaken/bewerken/verwijderen/toggle tegen een echte `kamers`-tabel) is niet end-to-end getest — wel de fail-safe lege-lijst-paden.

Nog niet gedaan in Fase 2 (komt terug in latere fases): kamerbeheer staat nog niet achter een intranet/login (`/kamers/beheer` is voor iedereen bereikbaar, net als `/kamers`) — dat volgt met Fase 4 (rollen/auth) en Fase 5 (intranet-split). Foto's zijn nu een los URL-veld, geen upload.

### Fase 3 — Eten bestellen per dagdeel ✅
- `order_food` had nog geen XML-definitie (werd alleen bevraagd, nooit gemigreerd); toegevoegd als [database/xml/order_food.xml](database/xml/order_food.xml) met een `dagdeel`-kolom (`ontbijt`/`lunch`/`diner`, default `ontbijt`) en `is_available`, naar het patroon van `kamers.xml`
- [app/Modules/Bestellen/Models/MenuItemModel.php](app/Modules/Bestellen/Models/MenuItemModel.php): `DAGDELEN`-constante, `byDagdeel()` (alleen beschikbare items voor één dagdeel, fail-safe naar `[]`) en `toggleAvailability()`, analoog aan `KamerModel`
- [app/Modules/Bestellen/Models/BasketModel.php](app/Modules/Bestellen/Models/BasketModel.php): winkelmandje-items dragen nu ook `dagdeel_item` mee
- Menubeheer (CRUD) toegevoegd aan [app/Modules/Bestellen/BestellenController.php](app/Modules/Bestellen/BestellenController.php) (`menuBeheer`/`menuCreate`/`menuStore`/`menuEdit`/`menuUpdate`/`menuDestroy`/`menuToggle`) met eigen views ([beheer.php](app/Modules/Bestellen/Views/BestellenView/beheer.php), [vorm.php](app/Modules/Bestellen/Views/BestellenView/vorm.php)), naast de publieke bestel-actie — zelfde side-by-side aanpak (geen intranet/auth-scheiding) als `KamerController` in Fase 2
- Bestelflow in de webapp ([index.php](app/Modules/Bestellen/Views/BestellenView/index.php)) toont tabs voor ontbijt/lunch/diner en filtert het menu op het gekozen dagdeel (`?dagdeel=...`); zonder keuze wordt het dagdeel automatisch bepaald op basis van het tijdstip (6–11 ontbijt, 11–17 lunch, verder diner)
- Rechten-toggle: de bestaande `bestellen`-feature dekt zowel de publieke bestelflow als het mandje, ongewijzigd sinds Fase 1
- Getest: `php -l` op alle nieuwe/gewijzigde bestanden, `php database/parse.php` om `schema.sql` te regenereren, en een lokale `php -S`-run van `/`, `/?dagdeel=lunch`, `/bestellen/beheer` en `/bestellen/nieuw`. Geen lokale MySQL beschikbaar, dus de databasegedreven kant (menu-item aanmaken/bewerken/verwijderen/toggle tegen een echte `order_food`-tabel) is niet end-to-end getest — wel de fail-safe lege-lijst-paden.

Nog niet gedaan in Fase 3 (komt terug in latere fases): menubeheer staat, net als kamerbeheer, nog niet achter een intranet/login.

### Fase 4 — Rechtensysteem uitbreiden ✅
- Tabel `gebruikers` toegevoegd ([database/xml/gebruikers.xml](database/xml/gebruikers.xml)): `naam`/`email`/`wachtwoord_hash`/`rol`/`is_actief`, met een geseede standaard adminaccount (`admin@bbeigenheimer.nl`, wachtwoord `eigenheimer2026` — direct wijzigen in productie). `rol` is een vrij tekstveld (`admin`/`medewerker`/`gast`, zie `UserModel::ROLES`), geen aparte rollen-tabel — zelfde eenvoud als de bestaande `features`-tabel.
- Nieuwe module `app/Shared/Auth`: [Auth.php](app/Shared/Auth/Auth.php) (sessiegedreven helper — `login()`/`logout()`/`check()`/`hasRole()`, geen aparte Model omdat het puur sessiestatus is, vergelijkbaar met `BasketModel`), [Models/UserModel.php](app/Shared/Auth/Models/UserModel.php) (extends `Core\Model`, tabel `gebruikers`), [AuthController.php](app/Shared/Auth/AuthController.php) (`showLogin`/`login`/`logout`) met een `Views/AuthView/login.php`-formulier.
- Beheeracties in [KamerController](app/Modules/Kamers/KamerController.php) (`beheer`/`create`/`store`/`edit`/`update`/`destroy`/`toggle`) en [BestellenController](app/Modules/Bestellen/BestellenController.php) (`menuBeheer`/`menuCreate`/`menuStore`/`menuEdit`/`menuUpdate`/`menuDestroy`/`menuToggle`) checken nu elk een `requireBeheerder()`-guard (`Auth::hasRole(['admin', 'medewerker'])`) en redirecten naar `/login` als die ontbreekt — zelfde "check + return aan het begin van de actie"-patroon als de bestaande `FeatureModel::isEnabled()`-checks.
- Instellingenscherm op `/rechten` ([RechtenController](app/Shared/Rechten/RechtenController.php), admin-only): toont alle rijen uit `features` met een aan/uit-knop per feature (`FeatureModel::all()` / `FeatureModel::toggle()`, nieuw toegevoegd aan [FeatureModel.php](app/Shared/Rechten/Models/FeatureModel.php)).
- Navbar toont Kamerbeheer/Menubeheer voor admin+medewerker, Rechten voor admin, en een in-/uitlogknop afhankelijk van sessiestatus.
- Getest: `php -l` op alle nieuwe/gewijzigde bestanden, `php database/parse.php`, en een lokale `php -S`-run: `/login` rendert met een echte CSRF-token, `/rechten` en `/kamers/beheer` redirecten (302) naar `/login` zonder sessie, en een POST naar `/login` zonder CSRF-token geeft (net als elke andere POST-route) een 419. Geen lokale MySQL beschikbaar, dus de echte inlogflow (wachtwoord verifiëren tegen een echte `gebruikers`-rij) is niet end-to-end getest.

Nog niet gedaan in Fase 4 (komt terug in latere fases): geen aparte `/intranet`-folder — beheerpagina's staan nog op dezelfde routes als de webapp, nu alleen afgeschermd door een rolcheck in plaats van door een aparte app (dat volgt in Fase 5). Geen wachtwoord-reset/registratiescherm — accounts worden vooralsnog handmatig in `gebruikers` gezet.

### Fase 5 — Intranet ✅
- Bestellingen worden nu gepersisteerd: [database/xml/bestellingen.xml](database/xml/bestellingen.xml) (tabel `bestellingen`: klant_naam, items als JSON, totaal, status, created_at) + [BestellingModel](app/Modules/Bestellen/Models/BestellingModel.php) (`openstaand()`/`afhandelen()`, fail-safe naar `[]` zoals de andere models). `BestellenController::checkout()` (route `POST /bestellen/afronden`) zet het sessie-mandje om in een bestelling en leegt het mandje ([BasketModel::clear()](app/Modules/Bestellen/Models/BasketModel.php)) — dit was nodig om het dashboard hieronder echte data te geven.
- Nieuwe module [app/Modules/Dashboard](app/Modules/Dashboard/DashboardController.php): intranet-startpagina met openstaande bestellingen (met "Afhandelen"-knop), kameroverzicht (beschikbaar/totaal) en menuoverzicht per dagdeel.
- De drie identieke `requireBeheerder()`-implementaties in `KamerController`/`BestellenController` zijn samengevoegd tot één `protected function requireBeheerder()` op [Core\Controller](app/Core/Controller.php), die `DashboardController` meteen hergebruikt.
- Folder-split naar `webapp/` en `intranet/`, elk met een eigen zelfstandige `public/` (front controller, `router.php`, `.htaccess`, `assets/`) — zie [Projectstructuur](#projectstructuur-webapp--intranet-) hierboven. Beide front controllers zetten een `APP_CONTEXT`-constante (`'webapp'`/`'intranet'`) vóór het laden van `app/bootstrap.php`; `app/bootstrap.php` zelf is ongewijzigd (APP_ROOT wordt al berekend t.o.v. zijn eigen locatie, niet t.o.v. de caller).
  - `webapp/public/index.php` registreert alleen publieke routes: `/`, `/kamers`, de mandje-routes en `/bestellen/afronden`.
  - `intranet/public/index.php` registreert login/logout, het dashboard op `/`, `/rechten`, en alle kamer-/menubeheer-CRUD-routes — dezelfde controllers als voorheen (`KamerController`, `BestellenController`), nu alleen bereikbaar via de intranet-app in plaats van side-by-side met de publieke routes.
  - De oude root `public/` en root `.htaccess` zijn verwijderd; lokaal draai je voortaan twee `php -S`-instanties (zie hieronder).
- Navbar gesplitst: [navbar-webapp.php](app/Views/partials/navbar-webapp.php) (de externe marketing-links, geen login/beheer meer) en [navbar-intranet.php](app/Views/partials/navbar-intranet.php) (Dashboard/Kamerbeheer/Menubeheer/Rechten/Uitloggen, alleen zichtbaar wanneer ingelogd). [app/Views/layouts/app.php](app/Views/layouts/app.php) kiest tussen beide op basis van `APP_CONTEXT`.
- Lokaal draaien (twee aparte terminals/poorten):
  ```
  php -S localhost:8000 -t webapp/public webapp/public/router.php
  php -S localhost:8001 -t intranet/public intranet/public/router.php
  ```
- Getest: `php -l` op alle nieuwe/gewijzigde bestanden, `php database/parse.php` om `schema.sql` te regenereren (nieuwe `bestellingen`-tabel), en lokale `php -S`-runs van beide apps: webapp (`/`, mand toevoegen/verwijderen, `/bestellen/afronden` met leeg en gevuld mandje, `/kamers`) en intranet (`/` zonder sessie redirect (302) naar `/login`, `/login` rendert met een echte CSRF-token, `/kamers/beheer` en `/bestellen/beheer` redirecten zonder sessie, POST zonder CSRF-token geeft 419 op beide apps). Geen lokale MySQL beschikbaar in deze omgeving (zelfde beperking als Fase 1–4), dus de echte data-flow (inloggen, dashboard met echte bestellingen/kamers, checkout die een rij wegschrijft) is niet end-to-end getest — wel de fail-safe/lege-lijst- en redirect-paden.

Nog niet gedaan in Fase 5 (komt terug in Fase 6): Bootstrap-styling is nog niet consistent tussen webapp en intranet (beide hergebruiken voorlopig dezelfde `home.css`/`navbar.css`), en er is geen validatie/foutafhandeling op de nieuwe checkout- en dashboard-formulieren.

### Fase 6 — Afwerking ✅
- Placeholder-navbar (`#headerTemp`, bisque achtergrond, ruwe `<ul><li>`-links) vervangen door een echte Bootstrap-navbar (`navbar navbar-expand-lg` + collapse) in zowel [navbar-webapp.php](app/Views/partials/navbar-webapp.php) als [navbar-intranet.php](app/Views/partials/navbar-intranet.php); dezelfde links/guards (`Auth::check()`/`Auth::hasRole()`) als voorheen, alleen de markup is herschreven. [footer.php](app/Views/partials/footer.php) kreeg een echte Bootstrap-footer i.p.v. de stub-tekst "footer is cool".
- `webapp/public/assets/css/{navbar,home}.css` en de identieke `intranet/public/assets/css/{navbar,home}.css` opgeschoond: dode `#ontbijtHeader`-regel (geen bijbehorende markup meer) en de oude `#headerTemp`/`#headerUi`-ids verwijderd, nu alleen nog een paar kleine overrides (logo-hoogte, lettertype) bovenop Bootstrap zelf. Blijven bewust twee losse kopieën, één per webroot, zoals de Fase 5-opzet.
- [Bestellen/Views/BestellenView/index.php](app/Modules/Bestellen/Views/BestellenView/index.php) omgezet van ruwe floated `<ul>`/`col-3`-markup naar dezelfde `card`-grid-stijl als [Kamers/Views/KamerView/index.php](app/Modules/Kamers/Views/KamerView/index.php); het winkelmandje is nu een `card` met `list-group-flush`. De `uitgeschakeld`-views van beide modules gebruiken nu `alert alert-info` i.p.v. een kale `<p>`.
- Nieuwe [app/Core/Validator.php](app/Core/Validator.php): twee stateless helpers (`required`, `nonNegativeNumber`), naast `Csrf`/`Uuid` in `Core/`. [KamerController](app/Modules/Kamers/KamerController.php) (`store`/`update`) en [BestellenController](app/Modules/Bestellen/BestellenController.php) (`menuStore`/`menuUpdate`/`checkout`) valideren nu naam/prijs/dagdeel/klantnaam en renderen bij een fout het formulier opnieuw met een `fouten`-lijst (`alert alert-danger`) en de al ingevulde waarden (`oud`), i.p.v. zomaar op te slaan of te redirecten — zelfde patroon als het bestaande `$fout`-veld in `AuthController::login()`, nu voor meerdere velden tegelijk.
- Getest: `php -l` op alle nieuwe/gewijzigde bestanden, en lokale `php -S`-runs van beide apps. Deze omgeving heeft geen `pdo_mysql`-driver (`PDO::getAvailableDrivers()` is leeg, ook al staat er wel iets op poort 3306), dus databasegedreven writes zijn nog steeds niet end-to-end te testen — wel de sessie-gedreven kant volledig: winkelmandje-item toevoegen/verwijderen, checkout met lege naam (toont "Naam is verplicht.", mandje blijft intact) en met CSRF-token ontbreekt (419), `/login` met onjuiste/ontbrekende gebruiker (bestaande foutmelding, fail-safe zonder DB), en `/rechten`/`/kamers/beheer` redirecten (302) naar `/login` zonder sessie.
