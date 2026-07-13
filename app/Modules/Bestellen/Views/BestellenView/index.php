<?php
/** @var array $menuItems */
/** @var array $basketItems */
/** @var string $dagdeel */
/** @var array $dagdelen */
/** @var string $csrfToken */
$labels = ['ontbijt' => 'Ontbijt', 'lunch' => 'Lunch', 'diner' => 'Diner'];
?>
<div class="container">
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
            <div class="col-3">
                <form method="post" action="/bestellen/mand">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="price_item" value="<?= htmlspecialchars($item['price']) ?>">
                    <input type="hidden" name="uuid_item" value="<?= htmlspecialchars($item['UUID']) ?>">
                    <input type="hidden" name="name_item" value="<?= htmlspecialchars($item['name']) ?>">
                    <input type="hidden" name="dagdeel_item" value="<?= htmlspecialchars($item['dagdeel']) ?>">
                    <input type="submit" class="button" value="+">
                </form>

                <?= htmlspecialchars($item['name']) ?><br>&euro;<?= htmlspecialchars($item['price']) ?>
            </div>
        <?php endforeach; ?>

        <?php if ($menuItems === []): ?>
            <p>Geen items beschikbaar voor <?= htmlspecialchars($labels[$dagdeel] ?? $dagdeel) ?>.</p>
        <?php endif; ?>
    </div>
</div>

<ul class="float-right list-group col-3">
    <?php foreach ($basketItems as $item): ?>
        <li class="list-group-item float-left">
            <form method="post" action="/bestellen/mand/<?= urlencode($item['basket_item_uuid']) ?>/verwijderen">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                <button type="submit" class="btn btn-danger float-left">
                    &nbsp;<i class="bi bi-trash"></i>&nbsp;
                </button>
            </form>
            &nbsp;<?= htmlspecialchars($item['name_item']) ?>
            <div class="float-right">&euro;<?= htmlspecialchars($item['price_item']) ?></div>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($basketItems !== []): ?>
    <form method="post" action="/bestellen/afronden" class="float-right col-3 mt-2">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="text" name="klant_naam" class="form-control mb-2" placeholder="Naam">
        <button type="submit" class="btn btn-success btn-block">Bestelling plaatsen</button>
    </form>
<?php endif; ?>
