<?php
/**
 * Bootstrap File Input Template
 * Variables: $field, $this (Renderer)
 */
$name = htmlspecialchars($field->getName());
$attrs = $this->renderAttributes($field->getAttributes());
$required = $field->isRequired() ? ' required' : '';
?>

<input 
    type="file" 
    class="form-control" 
    name="<?php echo $name; ?>" 
    id="<?php echo $name; ?>"
    <?php echo $required; ?>
    <?php echo $attrs; ?>
>

