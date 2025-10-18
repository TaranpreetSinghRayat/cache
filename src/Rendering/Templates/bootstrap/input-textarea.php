<?php
/**
 * Bootstrap Textarea Template
 * Variables: $field, $this (Renderer)
 */
$name = htmlspecialchars($field->getName());
$value = htmlspecialchars($field->getValue() ?? '');
$placeholder = htmlspecialchars($field->getPlaceholder());
$rows = $field->getRows() ?? 4;
$attrs = $this->renderAttributes($field->getAttributes());
$required = $field->isRequired() ? ' required' : '';
?>

<textarea 
    class="form-control" 
    name="<?php echo $name; ?>" 
    id="<?php echo $name; ?>" 
    rows="<?php echo $rows; ?>"
    <?php if ($placeholder): ?>placeholder="<?php echo $placeholder; ?>"<?php endif; ?>
    <?php echo $required; ?>
    <?php echo $attrs; ?>
><?php echo $value; ?></textarea>

