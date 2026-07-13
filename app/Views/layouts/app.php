<?php
/** @var string $content */
/** @var string $activeModule */
/** @var string $pageTitle */
/** @var string $csrfToken */
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle !== '' ? $pageTitle : 'B&B Eigenheimer') ?></title>
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/home.css">
</head>
<body>

<?php
$navbar = (defined('APP_CONTEXT') && APP_CONTEXT === 'intranet') ? 'navbar-intranet' : 'navbar-webapp';
require APP_ROOT . "/app/Views/partials/{$navbar}.php";
?>

<main>
    <?= $content ?>
</main>

<?php require APP_ROOT . '/app/Views/partials/footer.php'; ?>

</body>
</html>
