<?php
/**
 * Bootstrap Help Text Template
 * Variables: $field, $this (Renderer)
 */
?>

<small class="form-text text-muted d-block mt-1">
    <?php echo htmlspecialchars($field->getHelpText()); ?>
</small>

