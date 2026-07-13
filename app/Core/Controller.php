<?php

namespace App\Core;

use App\Shared\Auth\Auth;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = APP_ROOT . "/app/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View niet gevonden: {$view}");
        }

        $activeModule = $activeModule ?? '';
        $pageTitle = $pageTitle ?? '';
        $csrfToken = Csrf::token();

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require APP_ROOT . '/app/Views/layouts/app.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    /** Admin/medewerker-only; redirect naar /login als niet ingelogd met de juiste rol. */
    protected function requireBeheerder(): bool
    {
        if (Auth::hasRole(['admin', 'medewerker'])) {
            return true;
        }

        $this->redirect('/login');
        return false;
    }
}
