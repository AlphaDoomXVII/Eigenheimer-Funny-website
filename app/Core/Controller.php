<?php

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = APP_ROOT . "/app/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View niet gevonden: {$view}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $activeModule = $activeModule ?? '';
        $pageTitle = $pageTitle ?? '';
        $csrfToken = Csrf::token();
        require APP_ROOT . '/app/Views/layouts/app.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
