<?php

namespace App\Shared\Auth;

use App\Core\Controller;
use App\Shared\Auth\Models\UserModel;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
            return;
        }

        $this->render('Shared/Auth/Views/AuthView/login', [
            'fout' => null,
            'activeModule' => 'login',
            'pageTitle' => 'Inloggen',
        ]);
    }

    public function login(): void
    {
        $email = (string) ($_POST['email'] ?? '');
        $wachtwoord = (string) ($_POST['wachtwoord'] ?? '');

        $user = UserModel::findByEmail($email);
        if ($user === null || !password_verify($wachtwoord, $user['wachtwoord_hash'])) {
            $this->render('Shared/Auth/Views/AuthView/login', [
                'fout' => 'Onjuiste inloggegevens.',
                'activeModule' => 'login',
                'pageTitle' => 'Inloggen',
            ]);
            return;
        }

        Auth::login($user);
        $this->redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }
}
