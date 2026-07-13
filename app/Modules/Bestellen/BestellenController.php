<?php

namespace App\Modules\Bestellen;

use App\Core\Controller;
use App\Core\Uuid;
use App\Modules\Bestellen\Models\BasketModel;
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

        $dagdeel = $this->huidigDagdeel();

        $this->render('Modules/Bestellen/Views/BestellenView/index', [
            'menuItems' => MenuItemModel::byDagdeel($dagdeel),
            'basketItems' => BasketModel::items(),
            'dagdeel' => $dagdeel,
            'dagdelen' => MenuItemModel::DAGDELEN,
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

    public function menuBeheer(): void
    {
        $this->render('Modules/Bestellen/Views/BestellenView/beheer', [
            'menuItems' => MenuItemModel::all(),
            'dagdelen' => MenuItemModel::DAGDELEN,
            'activeModule' => 'bestellen',
            'pageTitle' => 'Menubeheer',
        ]);
    }

    public function menuCreate(): void
    {
        $this->render('Modules/Bestellen/Views/BestellenView/vorm', [
            'menuItem' => null,
            'dagdelen' => MenuItemModel::DAGDELEN,
            'activeModule' => 'bestellen',
            'pageTitle' => 'Nieuw menu-item',
        ]);
    }

    public function menuStore(): void
    {
        MenuItemModel::create([
            'UUID' => Uuid::generate(),
            'name' => (string) ($_POST['name'] ?? ''),
            'price' => (string) ($_POST['price'] ?? '0'),
            'dagdeel' => (string) ($_POST['dagdeel'] ?? MenuItemModel::DAGDELEN[0]),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ]);

        $this->redirect('/bestellen/beheer');
    }

    public function menuEdit(int $id): void
    {
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
        MenuItemModel::update($id, [
            'name' => (string) ($_POST['name'] ?? ''),
            'price' => (string) ($_POST['price'] ?? '0'),
            'dagdeel' => (string) ($_POST['dagdeel'] ?? MenuItemModel::DAGDELEN[0]),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ]);

        $this->redirect('/bestellen/beheer');
    }

    public function menuDestroy(int $id): void
    {
        MenuItemModel::delete($id);
        $this->redirect('/bestellen/beheer');
    }

    public function menuToggle(int $id): void
    {
        MenuItemModel::toggleAvailability($id);
        $this->redirect('/bestellen/beheer');
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
