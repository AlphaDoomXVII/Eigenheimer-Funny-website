<?php
/** @var array<string, bool> $features */
/** @var string $csrfToken */
?>
<div class="container">
    <h1>Rechten</h1>
    <p>Zet losse onderdelen van de website aan of uit.</p>

    <table class="table">
        <thead>
            <tr>
                <th>Feature</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($features as $feature => $enabled): ?>
                <tr>
                    <td><?= htmlspecialchars($feature) ?></td>
                    <td><?= $enabled ? 'Aan' : 'Uit' ?></td>
                    <td class="text-right">
                        <form method="post" action="/rechten/<?= htmlspecialchars($feature) ?>/toggle" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <button type="submit" class="btn btn-sm btn-warning">
                                <?= $enabled ? 'Uitzetten' : 'Aanzetten' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($features === []): ?>
                <tr><td colspan="3">Geen features gevonden.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
