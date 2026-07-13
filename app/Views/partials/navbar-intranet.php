<?php

use App\Shared\Auth\Auth;

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="https://bbeigenheimer.nl/wp-content/uploads/2021/03/bb-eigenheimer-logo_horizontaal.png" id="eigenheimerLogo" alt="B&amp;B Eigenheimer">
            Intranet
        </a>

        <?php if (Auth::check()): ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarIntranet" aria-controls="navbarIntranet" aria-expanded="false" aria-label="Menu wisselen">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarIntranet">
                <ul class="navbar-nav ml-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="/">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/kamers/beheer">Kamerbeheer</a></li>
                    <li class="nav-item"><a class="nav-link" href="/bestellen/beheer">Menubeheer</a></li>
                    <?php if (Auth::hasRole('admin')): ?>
                        <li class="nav-item"><a class="nav-link" href="/rechten">Rechten</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <form method="post" action="/logout">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Uitloggen</button>
                        </form>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>
