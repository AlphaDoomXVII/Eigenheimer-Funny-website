<?php

namespace App\Shared\Rechten;

use App\Core\Controller;
use App\Shared\Auth\Auth;
use App\Shared\Rechten\Models\FeatureModel;

class RechtenController extends Controller
{
    public function index(): void
    {
        if (!Auth::hasRole('admin')) {
            $this->redirect('/login');
            return;
        }

        $this->render('Shared/Rechten/Views/RechtenView/index', [
            'features' => FeatureModel::all(),
            'activeModule' => 'rechten',
            'pageTitle' => 'Rechten',
        ]);
    }

    public function toggle(string $feature): void
    {
        if (!Auth::hasRole('admin')) {
            $this->redirect('/login');
            return;
        }

        FeatureModel::toggle($feature);
        $this->redirect('/rechten');
    }
}
