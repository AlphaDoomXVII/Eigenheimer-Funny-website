<?php
/** @var array|null $menuItem */
/** @var array $dagdelen */
/** @var array $oud */
/** @var array $fouten */
/** @var string $csrfToken */
$labels = ['ontbijt' => 'Ontbijt', 'lunch' => 'Lunch', 'diner' => 'Diner'];
$oud = $oud ?? [];
$fouten = $fouten ?? [];
$actie = $menuItem === null ? '/bestellen' : '/bestellen/' . (int) $menuItem['id'];
$waarde = fn (string $veld, string $default = '') => htmlspecialchars($oud[$veld] ?? $menuItem[$veld] ?? $default);
$gekozenDagdeel = $oud['dagdeel'] ?? $menuItem['dagdeel'] ?? $dagdelen[0];
?>
<div class="container">
    <h1><?= $menuItem === null ? 'Nieuw menu-item' : 'Menu-item bewerken' ?></h1>

    <?php if ($fouten !== []): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($fouten as $fout): ?>
                    <li><?= htmlspecialchars($fout) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($actie) ?>">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="name">Naam</label>
            <input type="text" class="form-control" id="name" name="name" required
                   value="<?= $waarde('name') ?>">
        </div>

        <div class="form-group">
            <label for="price">Prijs</label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price"
                   value="<?= $waarde('price', '0') ?>">
        </div>

        <div class="form-group">
            <label for="dagdeel">Dagdeel</label>
            <select class="form-control" id="dagdeel" name="dagdeel">
                <?php foreach ($dagdelen as $optie): ?>
                    <option value="<?= htmlspecialchars($optie) ?>"
                        <?= $gekozenDagdeel === $optie ? 'selected' : '' ?>>
                        <?= htmlspecialchars($labels[$optie] ?? $optie) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="is_available" name="is_available"
                   <?= (array_key_exists('is_available', $oud) ? $oud['is_available'] : (empty($menuItem) || $menuItem['is_available'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_available">Beschikbaar</label>
        </div>

        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="/bestellen/beheer" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
