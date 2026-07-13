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
app/
  bootstrap.php              -> autoloader (PSR-4-achtig, App\ -> app/), sessie, timezone, .env
  Core/                      -> generieke bouwstenen, los van businesslogica
    Router.php               -> routing (GET/POST, {id}/{uuid}-placeholders, CSRF-check op POST)
    Controller.php            -> basis-controller: render() (view + layout), redirect()
    Database.php               -> PDO-singleton
    Model.php                   -> basis-CRUD-model (all/find/create/update/delete)
    Csrf.php                     -> CSRF-token per sessie
    Uuid.php                      -> UUID-generator
    SchemaParser.php               -> zet database/xml/*.xml om naar database/schema.sql
  Modules/
    Bestellen/                 -> één module = Controller + Models/ + Views/, zoals elke module in ticketsystemVHE
      BestellenController.php
      Models/
        MenuItemModel.php        -> extends Core\Model, tabel order_food
        BasketModel.php           -> sessie-gebaseerd (geen databasetabel, dus geen Core\Model)
      Views/BestellenView/
        index.php
        uitgeschakeld.php
  Shared/
    Rechten/Models/
      FeatureModel.php          -> rechtensysteem: aan/uit-schakelbare features
  Views/
    layouts/app.php              -> paginaskelet (head, navbar, footer)
    partials/navbar.php, footer.php
config/
  config.php                    -> DB-config, leest .env
database/
  xml/                            -> tabel-definities, één XML-bestand per tabel (bron van de waarheid)
    README.md                      -> uitleg XML-formaat
  parse.php                       -> genereert database/schema.sql uit database/xml/*.xml
  schema.sql                     -> gegenereerd bestand, niet handmatig bewerken
public/                          -> enige map die publiek bereikbaar hoort te zijn (webroot)
  index.php                       -> front controller: definieert routes, dispatcht
  router.php                       -> voor php -S (lokaal draaien zonder Apache)
  .htaccess                         -> alles naar index.php, behalve bestaande bestanden
  assets/css/                        -> home.css, navbar.css
.htaccess                          -> stuurt alles door naar public/
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

## Projectstructuur (doel: webapp + intranet)

```
/webapp/       -> publieke site: kamers bekijken, eten bestellen, contact
/intranet/     -> beheer: kamers, menu, dagdelen, rechten, bestellingen overzicht
```

De huidige `app/`-structuur is de gedeelde core (Router, Controller, Database, Model, Csrf). Zodra Fase 5 het intranet toevoegt, krijgt elke applicatie zijn eigen `public/` + moduleset, maar delen ze dezelfde `app/Core` en `app/Shared`-laag — net zoals ticketsystemVHE één `app/`-boom heeft voor al zijn modules.

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

### Fase 4 — Rechtensysteem uitbreiden
- Rollen (bijv. admin, medewerker, gast) met bijbehorende rechten
- Login/sessiebeheer koppelen aan rollen (nieuwe `app/Shared/Auth`-module, naar het patroon van ticketsystemVHE's `AuthController`)
- Intranet-pagina's afschermen op basis van rol
- Losse features (kamers, bestellen) per stuk aan/uit kunnen zetten vanuit een instellingenscherm

### Fase 5 — Intranet
- Dashboard met overzicht van openstaande bestellingen en kameroverzicht
- Beheerpagina's voor kamers, menu en rechten (bouwt voort op Fase 2–4)
- Losstaand van de webapp qua folder, deelt waar mogelijk dezelfde database-/model-laag

### Fase 6 — Afwerking
- Bootstrap-styling consistent maken over webapp en intranet
- Foutafhandeling en validatie op formulieren
- Testen van de volledige bestel- en kamerflow, inclusief het aan/uit zetten van features via het rechtensysteem
