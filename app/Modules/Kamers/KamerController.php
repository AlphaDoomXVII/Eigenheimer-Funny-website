<?php

namespace App\Modules\Kamers;

use App\Core\Controller;
use App\Core\Uuid;
use App\Modules\Kamers\Models\KamerModel;
use App\Shared\Rechten\Models\FeatureModel;

class KamerController extends Controller
{
    public function index(): void
    {
        if (!FeatureModel::isEnabled('kamers')) {
            $this->render('Modules/Kamers/Views/KamerView/uitgeschakeld', [
                'activeModule' => 'kamers',
                'pageTitle' => 'Kamers',
            ]);
            return;
        }

        $this->render('Modules/Kamers/Views/KamerView/index', [
            'kamers' => KamerModel::available(),
            'activeModule' => 'kamers',
            'pageTitle' => 'Kamers',
        ]);
    }

    public function beheer(): void
    {
        $this->render('Modules/Kamers/Views/KamerView/beheer', [
            'kamers' => KamerModel::all(),
            'activeModule' => 'kamers',
            'pageTitle' => 'Kamerbeheer',
        ]);
    }

    public function create(): void
    {
        $this->render('Modules/Kamers/Views/KamerView/vorm', [
            'kamer' => null,
            'activeModule' => 'kamers',
            'pageTitle' => 'Nieuwe kamer',
        ]);
    }

    public function store(): void
    {
        KamerModel::create([
            'UUID' => Uuid::generate(),
            'name' => (string) ($_POST['name'] ?? ''),
            'description' => (string) ($_POST['description'] ?? ''),
            'price' => (string) ($_POST['price'] ?? '0'),
            'photo_path' => (string) ($_POST['photo_path'] ?? ''),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ]);

        $this->redirect('/kamers/beheer');
    }

    public function edit(int $id): void
    {
        $kamer = KamerModel::find($id);
        if ($kamer === null) {
            http_response_code(404);
            echo '404 - Kamer niet gevonden.';
            return;
        }

        $this->render('Modules/Kamers/Views/KamerView/vorm', [
            'kamer' => $kamer,
            'activeModule' => 'kamers',
            'pageTitle' => 'Kamer bewerken',
        ]);
    }

    public function update(int $id): void
    {
        KamerModel::update($id, [
            'name' => (string) ($_POST['name'] ?? ''),
            'description' => (string) ($_POST['description'] ?? ''),
            'price' => (string) ($_POST['price'] ?? '0'),
            'photo_path' => (string) ($_POST['photo_path'] ?? ''),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ]);

        $this->redirect('/kamers/beheer');
    }

    public function destroy(int $id): void
    {
        KamerModel::delete($id);
        $this->redirect('/kamers/beheer');
    }

    public function toggle(int $id): void
    {
        KamerModel::toggleAvailability($id);
        $this->redirect('/kamers/beheer');
    }
}
