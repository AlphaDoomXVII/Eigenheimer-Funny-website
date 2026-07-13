<?php

namespace App\Modules\Bestellen;

use App\Core\Controller;
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

        $this->render('Modules/Bestellen/Views/BestellenView/index', [
            'menuItems' => MenuItemModel::all(),
            'basketItems' => BasketModel::items(),
            'activeModule' => 'bestellen',
            'pageTitle' => 'Bestellen',
        ]);
    }

    public function store(): void
    {
        BasketModel::add(
            (string) ($_POST['price_item'] ?? ''),
            (string) ($_POST['uuid_item'] ?? ''),
            (string) ($_POST['name_item'] ?? '')
        );

        $this->redirect('/');
    }

    public function destroy(string $basketItemUuid): void
    {
        BasketModel::remove($basketItemUuid);
        $this->redirect('/');
    }
}
