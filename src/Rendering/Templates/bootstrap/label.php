<?php
/**
 * Bootstrap Label Template
 * Variables: $field, $this (Renderer)
 */
$name = htmlspecialchars($field->getName());
$label = htmlspecialchars($field->getLabel());
$required = $field->isRequired() ? '<span class="text-danger">*</span>' : '';
?>

<label for="<?php echo $name; ?>" class="form-label">
    <?php echo $label; ?> <?php echo $required; ?>
</label>

