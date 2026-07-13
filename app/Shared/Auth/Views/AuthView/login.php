<?php
/** @var string|null $fout */
/** @var string $csrfToken */
?>
<div class="container">
    <h1>Inloggen</h1>

    <?php if ($fout !== null): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($fout) ?></div>
    <?php endif; ?>

    <form method="post" action="/login" style="max-width: 400px;">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="email">E-mailadres</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>

        <div class="form-group">
            <label for="wachtwoord">Wachtwoord</label>
            <input type="password" class="form-control" id="wachtwoord" name="wachtwoord" required>
        </div>

        <button type="submit" class="btn btn-primary">Inloggen</button>
    </form>
</div>
