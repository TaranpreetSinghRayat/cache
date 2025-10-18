<?php
/**
 * Bootstrap Select Template
 * Variables: $field, $this (Renderer)
 */
$name = htmlspecialchars($field->getName());
$value = $field->getValue();
$attrs = $this->renderAttributes($field->getAttributes());
$required = $field->isRequired() ? ' required' : '';
?>

<select 
    class="form-select" 
    name="<?php echo $name; ?>" 
    id="<?php echo $name; ?>"
    <?php echo $required; ?>
    <?php echo $attrs; ?>
>
    <option value="">-- Select --</option>
    <?php foreach ($field->getOptions() as $optValue => $optLabel): ?>
        <option value="<?php echo htmlspecialchars($optValue); ?>" <?php echo $value == $optValue ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($optLabel); ?>
        </option>
    <?php endforeach; ?>
</select>

