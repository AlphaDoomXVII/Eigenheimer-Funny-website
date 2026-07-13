<?php

namespace App\Modules\Kamers;

use App\Core\Controller;
use App\Core\Uuid;
use App\Core\Validator;
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
        if (!$this->requireBeheerder()) {
            return;
        }

        $this->render('Modules/Kamers/Views/KamerView/beheer', [
            'kamers' => KamerModel::all(),
            'activeModule' => 'kamers',
            'pageTitle' => 'Kamerbeheer',
        ]);
    }

    public function create(): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $this->render('Modules/Kamers/Views/KamerView/vorm', [
            'kamer' => null,
            'activeModule' => 'kamers',
            'pageTitle' => 'Nieuwe kamer',
        ]);
    }

    public function store(): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        $oud = $this->kamerInput();
        $fouten = $this->valideerKamer($oud);
        if ($fouten !== []) {
            $this->render('Modules/Kamers/Views/KamerView/vorm', [
                'kamer' => null,
                'oud' => $oud,
                'fouten' => $fouten,
                'activeModule' => 'kamers',
                'pageTitle' => 'Nieuwe kamer',
            ]);
            return;
        }

        KamerModel::create([
            'UUID' => Uuid::generate(),
            'name' => $oud['name'],
            'description' => $oud['description'],
            'price' => $oud['price'],
            'photo_path' => $oud['photo_path'],
            'is_available' => $oud['is_available'] ? 1 : 0,
        ]);

        $this->redirect('/kamers/beheer');
    }

    public function edit(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

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
        if (!$this->requireBeheerder()) {
            return;
        }

        $oud = $this->kamerInput();
        $fouten = $this->valideerKamer($oud);
        if ($fouten !== []) {
            $this->render('Modules/Kamers/Views/KamerView/vorm', [
                'kamer' => ['id' => $id],
                'oud' => $oud,
                'fouten' => $fouten,
                'activeModule' => 'kamers',
                'pageTitle' => 'Kamer bewerken',
            ]);
            return;
        }

        KamerModel::update($id, [
            'name' => $oud['name'],
            'description' => $oud['description'],
            'price' => $oud['price'],
            'photo_path' => $oud['photo_path'],
            'is_available' => $oud['is_available'] ? 1 : 0,
        ]);

        $this->redirect('/kamers/beheer');
    }

    public function destroy(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        KamerModel::delete($id);
        $this->redirect('/kamers/beheer');
    }

    public function toggle(int $id): void
    {
        if (!$this->requireBeheerder()) {
            return;
        }

        KamerModel::toggleAvailability($id);
        $this->redirect('/kamers/beheer');
    }

    private function kamerInput(): array
    {
        return [
            'name' => (string) ($_POST['name'] ?? ''),
            'description' => (string) ($_POST['description'] ?? ''),
            'price' => (string) ($_POST['price'] ?? '0'),
            'photo_path' => (string) ($_POST['photo_path'] ?? ''),
            'is_available' => isset($_POST['is_available']),
        ];
    }

    private function valideerKamer(array $input): array
    {
        $fouten = [];
        if (!Validator::required($input['name'])) {
            $fouten[] = 'Naam is verplicht.';
        }
        if (!Validator::nonNegativeNumber($input['price'])) {
            $fouten[] = 'Prijs moet een getal van 0 of hoger zijn.';
        }

        return $fouten;
    }
}
