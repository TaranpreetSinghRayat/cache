<?php
/**
 * Tailwind Text Input Template
 * Variables: $field, $this (Renderer)
 */
$name = htmlspecialchars($field->getName());
$value = htmlspecialchars($field->getValue() ?? '');
$placeholder = htmlspecialchars($field->getPlaceholder());
$inputType = $field->getInputType() ?? 'text';
$attrs = $this->renderAttributes($field->getAttributes());
$required = $field->isRequired() ? ' required' : '';
?>

<input 
    type="<?php echo $inputType; ?>" 
    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
    name="<?php echo $name; ?>" 
    id="<?php echo $name; ?>" 
    value="<?php echo $value; ?>"
    <?php if ($placeholder): ?>placeholder="<?php echo $placeholder; ?>"<?php endif; ?>
    <?php echo $required; ?>
    <?php echo $attrs; ?>
>

