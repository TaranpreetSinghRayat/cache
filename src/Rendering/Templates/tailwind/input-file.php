<?php
$name = htmlspecialchars($field->getName());
$attrs = $this->renderAttributes($field->getAttributes());
$required = $field->isRequired() ? ' required' : '';
?>

<input 
    type="file" 
    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
    name="<?php echo $name; ?>" 
    id="<?php echo $name; ?>"
    <?php echo $required; ?>
    <?php echo $attrs; ?>
>

