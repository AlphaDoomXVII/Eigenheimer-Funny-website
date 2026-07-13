<?php

use App\Shared\Auth\Auth;

?>
<header id="headerTemp">
    <img src="https://bbeigenheimer.nl/wp-content/uploads/2021/03/bb-eigenheimer-logo_horizontaal.png" id="eigenheimerLogo">
    <ul id="headerUi">
        <?php if (Auth::check()): ?>
            <li><a class="headerUiLinks" href="/">Dashboard</a></li>
            <li><a class="headerUiLinks" href="/kamers/beheer">Kamerbeheer</a></li>
            <li><a class="headerUiLinks" href="/bestellen/beheer">Menubeheer</a></li>
            <?php if (Auth::hasRole('admin')): ?>
                <li><a class="headerUiLinks" href="/rechten">Rechten</a></li>
            <?php endif; ?>
            <li>
                <form method="post" action="/logout" class="d-inline">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="headerUiLinks btn btn-link p-0">Uitloggen</button>
                </form>
            </li>
        <?php endif; ?>
    </ul>
</header>
