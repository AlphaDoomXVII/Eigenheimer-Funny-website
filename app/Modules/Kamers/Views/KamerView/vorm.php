<?php
/** @var array|null $kamer */
/** @var string $csrfToken */
$actie = $kamer === null ? '/kamers' : '/kamers/' . (int) $kamer['id'];
?>
<div class="container">
    <h1><?= $kamer === null ? 'Nieuwe kamer' : 'Kamer bewerken' ?></h1>

    <form method="post" action="<?= htmlspecialchars($actie) ?>">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="name">Naam</label>
            <input type="text" class="form-control" id="name" name="name" required
                   value="<?= htmlspecialchars($kamer['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="description">Beschrijving</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($kamer['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Prijs per nacht</label>
            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price"
                   value="<?= htmlspecialchars($kamer['price'] ?? '0') ?>">
        </div>

        <div class="form-group">
            <label for="photo_path">Foto (URL)</label>
            <input type="text" class="form-control" id="photo_path" name="photo_path"
                   value="<?= htmlspecialchars($kamer['photo_path'] ?? '') ?>">
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="is_available" name="is_available"
                   <?= empty($kamer) || $kamer['is_available'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_available">Beschikbaar</label>
        </div>

        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="/kamers/beheer" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
