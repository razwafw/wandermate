<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

global $modalId, $modalTitle, $modalContent, $modalFooter;
?>

<div
    id="<?= $modalId; ?>"
    class="modal"
>
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title"><?= $modalTitle; ?></h2>
            <span class="close">&times;</span>
        </div>

        <?= $modalContent; ?>

        <div class="modal-footer">
            <?= $modalFooter; ?>
        </div>
    </div>
</div>

<script type="module">
    import { closeModal } from "./modal.js";

    const closeButton = document.querySelector('#<?= $modalId; ?> .close');

    closeButton.addEventListener("click", function () {
        closeModal('<?= $modalId; ?>');
    });

    window.addEventListener("click", function (event) {
        const modal = document.getElementById('<?= $modalId; ?>');

        if (event.target === modal) {
            closeModal('<?= $modalId; ?>');
        }
    });
</script>
