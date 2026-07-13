<?php
/** @var array $menuItems */
/** @var string $csrfToken */
$labels = ['ontbijt' => 'Ontbijt', 'lunch' => 'Lunch', 'diner' => 'Diner'];
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Menubeheer</h1>
        <a href="/bestellen/nieuw" class="btn btn-primary">Nieuw menu-item</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Naam</th>
                <th>Dagdeel</th>
                <th>Prijs</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menuItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($labels[$item['dagdeel']] ?? $item['dagdeel']) ?></td>
                    <td>&euro;<?= htmlspecialchars($item['price']) ?></td>
                    <td><?= $item['is_available'] ? 'Actief' : 'Uitgeschakeld' ?></td>
                    <td class="text-right">
                        <a href="/bestellen/<?= (int) $item['id'] ?>/bewerken" class="btn btn-sm btn-secondary">Bewerken</a>

                        <form method="post" action="/bestellen/<?= (int) $item['id'] ?>/toggle" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <button type="submit" class="btn btn-sm btn-warning">
                                <?= $item['is_available'] ? 'Deactiveren' : 'Activeren' ?>
                            </button>
                        </form>

                        <form method="post" action="/bestellen/<?= (int) $item['id'] ?>/verwijderen" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($menuItems === []): ?>
                <tr><td colspan="5">Nog geen menu-items toegevoegd.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
