<?php
/**
 * Bootstrap Text Input Template
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
    class="form-control" 
    name="<?php echo $name; ?>" 
    id="<?php echo $name; ?>" 
    value="<?php echo $value; ?>"
    <?php if ($placeholder): ?>placeholder="<?php echo $placeholder; ?>"<?php endif; ?>
    <?php echo $required; ?>
    <?php echo $attrs; ?>
>

