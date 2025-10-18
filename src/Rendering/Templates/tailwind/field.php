<?php
/**
 * Tailwind Field Template
 * Variables: $field, $error, $this (Renderer)
 */
$hasError = !empty($error);
$fieldType = $field->getType();
$fieldName = htmlspecialchars($field->getName());
?>

<div class="mb-4 form-group <?php echo $hasError ? 'has-error' : ''; ?>" data-field-name="<?php echo $fieldName; ?>">
    <?php if ($fieldType !== 'hidden' && $fieldType !== 'submit' && $fieldType !== 'reset' && $fieldType !== 'button'): ?>
        <?php echo $this->renderLabel($field); ?>
    <?php endif; ?>

    <?php echo $this->renderFieldInput($field); ?>

    <?php if ($hasError): ?>
        <?php echo $this->renderError($field, $error); ?>
    <?php endif; ?>

    <?php if ($field->getHelpText()): ?>
        <?php echo $this->renderHelp($field); ?>
    <?php endif; ?>
</div>

