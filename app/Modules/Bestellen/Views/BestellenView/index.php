<?php
/** @var array $menuItems */
/** @var array $basketItems */
/** @var string $dagdeel */
/** @var array $dagdelen */
/** @var array $fouten */
/** @var array $oud */
/** @var string $csrfToken */
$labels = ['ontbijt' => 'Ontbijt', 'lunch' => 'Lunch', 'diner' => 'Diner'];
$fouten = $fouten ?? [];
$oud = $oud ?? [];
?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <ul class="nav nav-tabs mb-3">
                <?php foreach ($dagdelen as $optie): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $optie === $dagdeel ? 'active' : '' ?>" href="/?dagdeel=<?= urlencode($optie) ?>">
                            <?= htmlspecialchars($labels[$optie] ?? $optie) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="row">
                <?php foreach ($menuItems as $item): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                                <p class="card-text">&euro;<?= htmlspecialchars($item['price']) ?></p>
                                <form method="post" action="/bestellen/mand">
                                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="price_item" value="<?= htmlspecialchars($item['price']) ?>">
                                    <input type="hidden" name="uuid_item" value="<?= htmlspecialchars($item['UUID']) ?>">
                                    <input type="hidden" name="name_item" value="<?= htmlspecialchars($item['name']) ?>">
                                    <input type="hidden" name="dagdeel_item" value="<?= htmlspecialchars($item['dagdeel']) ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Toevoegen</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($menuItems === []): ?>
                    <div class="col-12">
                        <p>Geen items beschikbaar voor <?= htmlspecialchars($labels[$dagdeel] ?? $dagdeel) ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Winkelmandje</div>

                <?php if ($fouten !== []): ?>
                    <div class="card-body pb-0">
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($fouten as $fout): ?>
                                    <li><?= htmlspecialchars($fout) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <ul class="list-group list-group-flush">
                    <?php foreach ($basketItems as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($item['name_item']) ?> &mdash; &euro;<?= htmlspecialchars($item['price_item']) ?></span>
                            <form method="post" action="/bestellen/mand/<?= urlencode($item['basket_item_uuid']) ?>/verwijderen">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>

                    <?php if ($basketItems === []): ?>
                        <li class="list-group-item">Nog niets in het mandje.</li>
                    <?php endif; ?>
                </ul>

                <?php if ($basketItems !== []): ?>
                    <div class="card-body">
                        <form method="post" action="/bestellen/afronden">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="text" name="klant_naam" class="form-control mb-2" placeholder="Naam"
                                   value="<?= htmlspecialchars($oud['klant_naam'] ?? '') ?>">
                            <button type="submit" class="btn btn-success btn-block">Bestelling plaatsen</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
