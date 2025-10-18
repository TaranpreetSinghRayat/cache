<?php
/**
 * Bootstrap Radio Template
 * Variables: $field, $this (Renderer)
 */
$name = htmlspecialchars($field->getName());
$value = $field->getValue();
$inline = $field->isInline();
$required = $field->isRequired() ? ' required' : '';
?>

<div class="<?php echo $inline ? 'form-check-inline' : ''; ?>">
    <?php foreach ($field->getOptions() as $optValue => $optLabel): ?>
        <div class="form-check">
            <input 
                class="form-check-input" 
                type="radio" 
                name="<?php echo $name; ?>" 
                id="<?php echo $name; ?>_<?php echo htmlspecialchars($optValue); ?>" 
                value="<?php echo htmlspecialchars($optValue); ?>"
                <?php echo $value == $optValue ? 'checked' : ''; ?>
                <?php echo $required; ?>
            >
            <label class="form-check-label" for="<?php echo $name; ?>_<?php echo htmlspecialchars($optValue); ?>">
                <?php echo htmlspecialchars($optLabel); ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>

