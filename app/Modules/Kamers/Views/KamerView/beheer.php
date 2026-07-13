<?php
/** @var array $kamers */
/** @var string $csrfToken */
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Kamerbeheer</h1>
        <a href="/kamers/nieuw" class="btn btn-primary">Nieuwe kamer</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Naam</th>
                <th>Prijs</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kamers as $kamer): ?>
                <tr>
                    <td><?= htmlspecialchars($kamer['name']) ?></td>
                    <td>&euro;<?= htmlspecialchars($kamer['price']) ?></td>
                    <td><?= $kamer['is_available'] ? 'Actief' : 'Uitgeschakeld' ?></td>
                    <td class="text-right">
                        <a href="/kamers/<?= (int) $kamer['id'] ?>/bewerken" class="btn btn-sm btn-secondary">Bewerken</a>

                        <form method="post" action="/kamers/<?= (int) $kamer['id'] ?>/toggle" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <button type="submit" class="btn btn-sm btn-warning">
                                <?= $kamer['is_available'] ? 'Deactiveren' : 'Activeren' ?>
                            </button>
                        </form>

                        <form method="post" action="/kamers/<?= (int) $kamer['id'] ?>/verwijderen" class="d-inline">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($kamers === []): ?>
                <tr><td colspan="4">Nog geen kamers toegevoegd.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
