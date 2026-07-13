<?php

namespace App\Modules\Bestellen;

use App\Core\Controller;
use App\Core\Uuid;
use App\Core\Validator;
use App\Modules\Bestellen\Models\BasketModel;
use App\Modules\Bestellen\Models\BestellingModel;
use App\Modules\Bestellen\Models\MenuItemModel;
use App\Shared\Rechten\Models\FeatureModel;

class BestellenController extends Controller
{
    public function index(): void
    {
        if (!FeatureModel::isEnabled('bestellen')) {
            $this->render('Modules/Bestellen/Views/BestellenView/uitgeschakeld', [
                'activeModule' => 'bestellen',
                'pageTitle' => 'Bestellen',
            ]);
            return;
        }

        $this->renderBestellen();
    }

    private function renderBestellen(array $fouten = [], array $oud = []): void
    {
        $dagdeel = $this->huidigDagdeel();

        $this->render('Modules/Bestellen/Views/BestellenView/index', [
            'menuItems' => MenuItemModel::byDagdeel($dagdeel),
            'basketItems' => BasketModel::items(),
            'dagdeel' => $dagdeel,
            'dagdelen' => MenuItemModel::DAGDELEN,
            'fouten' => $fouten,
            'oud' => $oud,
            'activeModule' => 'bestellen',
            'pageTitle' => 'Bestellen',
        ]);
    }

    public function store(): void
    {
        BasketModel::add(
            (string) ($_POST['price_item'] ?? ''),
            (string) ($_POST['uuid_item'] ?? ''),
            (string) ($_POST['name_item'] ?? ''),
            (string) ($_POST['dagdeel_item'] ?? '')
        );

        $this->redirect('/');
    }

    public function destroy(string $basketItemUuid): void
    {
        BasketModel::remove($basketItemUuid);
        $this->redirect('/');
    }

    public function checkout(): void
    {
        $items = BasketModel::items();
        if ($items === []) {
            $this->redirect('/');
            return;
        }

        $klantNaam = (string) ($_POST['klant_naam'] ?? '');
        if (!Validator::required($klantNaam)) {
            $this->renderBestellen(['Naam is verplicht.'], ['klant_naam' => $klantNaam]);
            return;
        }

        $totaal = array_sum(array_map(fn (array $item) => (float) $item['price_item'], $items));

        BestellingModel::create([
            'UUID' => Uuid::generate(),
            'klant_naam' => $klantNaam,
            'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
            'totaal' => (string) $totaal,
            'status' => 'openstaand',
        ]);

        BasketModel::clear();
        $this->redirect('/');
    }

    public function menuBeheer(): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $this->render('Modules/Bestellen/Views/BestellenView/beheer', [
            'menuItems' => MenuItemModel::all(),
            'dagdelen' => MenuItemModel::DAGDELEN,
            'activeModule' => 'bestellen',
            'pageTitle' => 'Menubeheer',
        ]);
    }

    public function menuCreate(): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $this->render('Modules/Bestellen/Views/BestellenView/vorm', [
            'menuItem' => null,
            'dagdelen' => MenuItemModel::DAGDELEN,
            'activeModule' => 'bestellen',
            'pageTitle' => 'Nieuw menu-item',
        ]);
    }

    public function menuStore(): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $oud = $this->menuInput();
        $fouten = $this->valideerMenuItem($oud);
        if ($fouten !== []) {
            $this->render('Modules/Bestellen/Views/BestellenView/vorm', [
                'menuItem' => null,
                'oud' => $oud,
                'fouten' => $fouten,
                'dagdelen' => MenuItemModel::DAGDELEN,
                'activeModule' => 'bestellen',
                'pageTitle' => 'Nieuw menu-item',
            ]);
            return;
        }

        MenuItemModel::create([
            'UUID' => Uuid::generate(),
            'name' => $oud['name'],
            'price' => $oud['price'],
            'dagdeel' => $oud['dagdeel'],
            'is_available' => $oud['is_available'] ? 1 : 0,
        ]);

        $this->redirect('/bestellen/beheer');
    }

    public function menuEdit(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $menuItem = MenuItemModel::find($id);
        if ($menuItem === null) {
            http_response_code(404);
            echo '404 - Menu-item niet gevonden.';
            return;
        }

        $this->render('Modules/Bestellen/Views/BestellenView/vorm', [
            'menuItem' => $menuItem,
            'dagdelen' => MenuItemModel::DAGDELEN,
            'activeModule' => 'bestellen',
            'pageTitle' => 'Menu-item bewerken',
        ]);
    }

    public function menuUpdate(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $oud = $this->menuInput();
        $fouten = $this->valideerMenuItem($oud);
        if ($fouten !== []) {
            $this->render('Modules/Bestellen/Views/BestellenView/vorm', [
                'menuItem' => ['id' => $id],
                'oud' => $oud,
                'fouten' => $fouten,
                'dagdelen' => MenuItemModel::DAGDELEN,
                'activeModule' => 'bestellen',
                'pageTitle' => 'Menu-item bewerken',
            ]);
            return;
        }

        MenuItemModel::update($id, [
            'name' => $oud['name'],
            'price' => $oud['price'],
            'dagdeel' => $oud['dagdeel'],
            'is_available' => $oud['is_available'] ? 1 : 0,
        ]);

        $this->redirect('/bestellen/beheer');
    }

    public function menuDestroy(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        MenuItemModel::delete($id);
        $this->redirect('/bestellen/beheer');
    }

    public function menuToggle(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        MenuItemModel::toggleAvailability($id);
        $this->redirect('/bestellen/beheer');
    }

    private function menuInput(): array
    {
        return [
            'name' => (string) ($_POST['name'] ?? ''),
            'price' => (string) ($_POST['price'] ?? '0'),
            'dagdeel' => (string) ($_POST['dagdeel'] ?? MenuItemModel::DAGDELEN[0]),
            'is_available' => isset($_POST['is_available']),
        ];
    }

    private function valideerMenuItem(array $input): array
    {
        $fouten = [];
        if (!Validator::required($input['name'])) {
            $fouten[] = 'Naam is verplicht.';
        }
        if (!Validator::nonNegativeNumber($input['price'])) {
            $fouten[] = 'Prijs moet een getal van 0 of hoger zijn.';
        }
        if (!in_array($input['dagdeel'], MenuItemModel::DAGDELEN, true)) {
            $fouten[] = 'Kies een geldig dagdeel.';
        }

        return $fouten;
    }

    private function huidigDagdeel(): string
    {
        $gekozen = (string) ($_GET['dagdeel'] ?? '');
        if (in_array($gekozen, MenuItemModel::DAGDELEN, true)) {
            return $gekozen;
        }

        $uur = (int) date('G');
        return match (true) {
            $uur >= 6 && $uur < 11 => 'ontbijt',
            $uur >= 11 && $uur < 17 => 'lunch',
            default => 'diner',
        };
    }
}
