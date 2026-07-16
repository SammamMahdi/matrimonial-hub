<?php

use App\Core\Flash;

$messages = Flash::drain();

if ($messages === []) {
    return;
}

$icons = ['success' => 'check-circle', 'error' => 'x-circle', 'info' => 'info'];
?>
<div class="flash-stack" role="status" aria-live="polite">
    <?php foreach ($messages as $message): ?>
        <?php $type = in_array($message['type'], ['success', 'error', 'info'], true) ? $message['type'] : 'info'; ?>
        <div class="flash flash-<?= e($type) ?>" data-flash>
            <?= icon($icons[$type], 20, 'flash-icon') ?>
            <p class="grow small"><?= e($message['message']) ?></p>
            <button class="flash-close" type="button" data-flash-close aria-label="Dismiss">
                <?= icon('x', 15) ?>
            </button>
        </div>
    <?php endforeach; ?>
</div>
