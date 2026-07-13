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

## Analyse huidige codebase

De repository bevat al een werkend begin van een lichte front-controller MVC-opzet:

- [index.php](index.php) — front controller: leest `?controller=` en `?action=` uit de querystring, laadt de bijpassende class uit [controllers/](controllers/) en roept de actie aan.
- [controllers/](controllers/) — o.a. `index.php` (basket-flow), `database.php` (PDO-connectie + `safequery`), `dataset.php` (UUID-generator), `login.php`, `contact.php`.
- [model/home.php](model/home.php) — bevat op dit moment zowel databaselogica als sessie-gebaseerde winkelmandlogica (`additem_basket`, `remove_item`) én view-aanroepen door elkaar.
- [view/UI.php](view/UI.php) — statische UI-helperklasse (navbar, footer, item-lijst, mandweergave).

**Bruikbaar en te behouden:**
- Front controller patroon in [index.php](index.php)
- `connect_Database::safequery()` als basis voor een generieke database-laag
- `dataset::guid()` voor unieke ID's (bruikbaar voor kamers, bestellingen, menu-items)
- Sessie-gebaseerde basket-aanpak (patroon herbruikbaar voor eten bestellen per dagdeel)

**Moet gerefactored worden:**
- Model en view zijn nu vermengd in [model/home.php](model/home.php) — databaselogica, sessielogica en het aanroepen van views horen gescheiden te worden.
- Geen rechten-/rollensysteem aanwezig — nodig om features aan/uit te zetten.
- Geen kamer- of menubeheer (tabellen/models) — alleen een `order_food`-tabel wordt al bevraagd.
- Dubbele/verouderde controllers ([controllers/indexStan.php](controllers/indexStan.php)) opruimen of samenvoegen.

## Projectstructuur (doel)

```
/webapp/       -> publieke site: kamers bekijken, eten bestellen, contact
/intranet/     -> beheer: kamers, menu, dagdelen, rechten, bestellingen overzicht
```

Elke subfolder krijgt zijn eigen `index.php` front controller, `controllers/`, `model/`, `view/`, naar het patroon dat al in de root staat, en volgens de CRUD/MVC-opzet uit ticketsystemVHE.

## Meervoudig plan (fasering)

### Fase 1 — Fundament & opschonen
- Bestaande MVC-code scheiden: databaselogica los van sessielogica los van view-rendering
- Front controller herbruikbaar maken voor meerdere applicaties (webapp + intranet)
- Verouderde/dubbele controllers opruimen
- Basisrechtensysteem opzetten (tabel `permissions`/`features`, helper om te checken of een feature actief is)

### Fase 2 — Kamerbeheer
- Database-model voor kamers (naam, beschrijving, prijs, beschikbaarheid, foto's)
- CRUD in het intranet (toevoegen, bewerken, verwijderen, activeren/deactiveren)
- Weergave van beschikbare kamers in de webapp
- Rechten-toggle: kamerbeheer volledig uit te zetten via het rechtensysteem

### Fase 3 — Eten bestellen per dagdeel
- Uitbreiden van bestaand `order_food`/basket-patroon met een `dagdeel` (ontbijt/lunch/diner)
- Menubeheer (CRUD) in het intranet, per dagdeel
- Bestelflow in de webapp: alleen items tonen die bij het huidige/gekozen dagdeel horen
- Rechten-toggle: bestelsysteem volledig uit te zetten via het rechtensysteem

### Fase 4 — Rechtensysteem uitbreiden
- Rollen (bijv. admin, medewerker, gast) met bijbehorende rechten
- Login/sessiebeheer koppelen aan rollen ([controllers/login.php](controllers/login.php) uitbreiden)
- Intranet-pagina's afschermen op basis van rol
- Losse features (kamers, bestellen) per stuk aan/uit kunnen zetten vanuit een instellingenscherm

### Fase 5 — Intranet
- Dashboard met overzicht van openstaande bestellingen en kameroverzicht
- Beheerpagina's voor kamers, menu en rechten (bouwt voort op Fase 2–4)
- Losstaand van de webapp qua folder, deelt waar mogelijk dezelfde database-/model-laag

### Fase 6 — Afwerking
- Bootstrap-styling consistent maken over webapp en intranet
- Foutafhandeling en validatie op formulieren (o.a. [model/post.php](model/post.php) afmaken — nu alleen een print_r-stub)
- Testen van de volledige bestel- en kamerflow, inclusief het aan/uit zetten van features via het rechtensysteem
