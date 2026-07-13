<?php
/** @var array|null $kamer */
/** @var array $oud */
/** @var array $fouten */
/** @var string $csrfToken */
$oud = $oud ?? [];
$fouten = $fouten ?? [];
$actie = $kamer === null ? '/kamers' : '/kamers/' . (int) $kamer['id'];
$waarde = fn (string $veld, string $default = '') => htmlspecialchars($oud[$veld] ?? $kamer[$veld] ?? $default);
?>
<div class="container">
    <h1><?= $kamer === null ? 'Nieuwe kamer' : 'Kamer bewerken' ?></h1>

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
            <label for="description">Beschrijving</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= $waarde('description') ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Prijs per nacht</label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price"
                   value="<?= $waarde('price', '0') ?>">
        </div>

        <div class="form-group">
            <label for="photo_path">Foto (URL)</label>
            <input type="text" class="form-control" id="photo_path" name="photo_path"
                   value="<?= $waarde('photo_path') ?>">
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="is_available" name="is_available"
                   <?= (array_key_exists('is_available', $oud) ? $oud['is_available'] : (empty($kamer) || $kamer['is_available'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_available">Beschikbaar</label>
        </div>

        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="/kamers/beheer" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
