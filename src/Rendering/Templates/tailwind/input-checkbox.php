<?php
$name = htmlspecialchars($field->getName());
$value = $field->getValue();
$inline = $field->isInline();
$required = $field->isRequired() ? ' required' : '';
?>

<div class="<?php echo $inline ? 'flex gap-4' : 'space-y-2'; ?>">
    <?php foreach ($field->getOptions() as $optValue => $optLabel): ?>
        <div class="flex items-center">
            <input 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                type="checkbox" 
                name="<?php echo $name; ?>[]" 
                id="<?php echo $name; ?>_<?php echo htmlspecialchars($optValue); ?>" 
                value="<?php echo htmlspecialchars($optValue); ?>"
                <?php echo is_array($value) && in_array($optValue, $value) ? 'checked' : ''; ?>
                <?php echo $required; ?>
            >
            <label class="ml-2 block text-sm text-gray-900" for="<?php echo $name; ?>_<?php echo htmlspecialchars($optValue); ?>">
                <?php echo htmlspecialchars($optLabel); ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>

