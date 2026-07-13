<?php
/** @var array $bestellingen */
/** @var int $kamersTotaal */
/** @var int $kamersBeschikbaar */
/** @var array $menuPerDagdeel */
/** @var string $csrfToken */
$labels = ['ontbijt' => 'Ontbijt', 'lunch' => 'Lunch', 'diner' => 'Diner'];
?>
<div class="container">
    <h1 class="mb-3">Dashboard</h1>

    <div class="row mb-4">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Kamers</h5>
                    <p class="card-text"><?= $kamersBeschikbaar ?> van de <?= $kamersTotaal ?> beschikbaar</p>
                </div>
            </div>
        </div>
        <?php foreach ($menuPerDagdeel as $dagdeel => $stats): ?>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($labels[$dagdeel] ?? $dagdeel) ?></h5>
                        <p class="card-text"><?= $stats['beschikbaar'] ?> van de <?= $stats['totaal'] ?> beschikbaar</p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="mb-3">Openstaande bestellingen</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Klant</th>
                <th>Items</th>
                <th>Totaal</th>
                <th>Geplaatst</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bestellingen as $bestelling): ?>
                <?php $items = json_decode($bestelling['items'], true) ?? []; ?>
                <tr>
                    <td><?= htmlspecialchars($bestelling['klant_naam'] !== '' ? $bestelling['klant_naam'] : '-') ?></td>
                    <td><?= htmlspecialchars(implode(', ', array_column($items, 'name_item'))) ?></td>
                    <td>&euro;<?= htmlspecialchars($bestelling['totaal']) ?></td>
                    <td><?= htmlspecialchars($bestelling['created_at']) ?></td>
                    <td class="text-right">
                        <form method="post" action="/bestellingen/<?= (int) $bestelling['id'] ?>/afhandelen" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <button type="submit" class="btn btn-sm btn-success">Afhandelen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($bestellingen === []): ?>
                <tr><td colspan="5">Geen openstaande bestellingen.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
