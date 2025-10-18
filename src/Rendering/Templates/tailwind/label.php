<?php
$name = htmlspecialchars($field->getName());
$label = htmlspecialchars($field->getLabel());
$required = $field->isRequired() ? '<span class="text-red-500">*</span>' : '';
?>

<label for="<?php echo $name; ?>" class="block text-sm font-medium text-gray-700 mb-1">
    <?php echo $label; ?> <?php echo $required; ?>
</label>

