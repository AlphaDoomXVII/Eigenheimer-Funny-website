<?php

namespace App\Modules\Dashboard;

use App\Core\Controller;
use App\Modules\Bestellen\Models\BestellingModel;
use App\Modules\Bestellen\Models\MenuItemModel;
use App\Modules\Kamers\Models\KamerModel;

class DashboardController extends Controller
{
    public function index(): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $kamers = KamerModel::all();
        $menuItems = MenuItemModel::all();

        $this->render('Modules/Dashboard/Views/DashboardView/index', [
            'bestellingen' => BestellingModel::openstaand(),
            'kamersTotaal' => count($kamers),
            'kamersBeschikbaar' => count(array_filter($kamers, fn (array $k) => (bool) $k['is_available'])),
            'menuPerDagdeel' => $this->menuPerDagdeel($menuItems),
            'activeModule' => 'dashboard',
            'pageTitle' => 'Dashboard',
        ]);
    }

    public function afhandelen(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        BestellingModel::afhandelen($id);
        $this->redirect('/');
    }

    private function menuPerDagdeel(array $menuItems): array
    {
        $overzicht = array_fill_keys(MenuItemModel::DAGDELEN, ['totaal' => 0, 'beschikbaar' => 0]);

        foreach ($menuItems as $item) {
            $dagdeel = $item['dagdeel'];
            if (!isset($overzicht[$dagdeel])) {
                continue;
            }

            $overzicht[$dagdeel]['totaal']++;
            if ($item['is_available']) {
                $overzicht[$dagdeel]['beschikbaar']++;
            }
        }

        return $overzicht;
    }
}
