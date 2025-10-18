<?php
$name = htmlspecialchars($field->getName());
$value = $field->getValue();
$attrs = $this->renderAttributes($field->getAttributes());
$required = $field->isRequired() ? ' required' : '';
?>

<select 
    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
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

