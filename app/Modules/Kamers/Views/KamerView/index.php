<?php
/** @var array $kamers */
?>
<div class="container">
    <div class="row">
        <?php foreach ($kamers as $kamer): ?>
            <div class="col-4 mb-4">
                <div class="card h-100">
                    <?php if ($kamer['photo_path'] !== ''): ?>
                        <img src="<?= htmlspecialchars($kamer['photo_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($kamer['name']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($kamer['name']) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($kamer['description'])) ?></p>
                        <p class="card-text">&euro;<?= htmlspecialchars($kamer['price']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($kamers === []): ?>
            <p>Er zijn op dit moment geen kamers beschikbaar.</p>
        <?php endif; ?>
    </div>
</div>
