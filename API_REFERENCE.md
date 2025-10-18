# Tweekersnut Forms Library - API Reference

## FormBuilder Class

### Constructor
```php
new FormBuilder(string $name, string $theme = 'bootstrap')
```

### Methods

#### Form Configuration
```php
$form->method(string $method): self          // Set HTTP method (GET, POST)
$form->action(string $action): self          // Set form action URL
$form->attributes(array $attrs): self        // Set custom HTML attributes
$form->addClass(string $class): self         // Add CSS class
$form->id(string $id): self                  // Set form ID
```

#### Field Management
```php
$form->field(Field $field): self             // Add field to form
$form->getFields(): array                    // Get all fields
$form->getField(string $name): ?Field       // Get specific field
```

#### CSRF Protection
```php
$form->withCsrfToken(string $token): self   // Add CSRF token
$form->hasCsrfToken(): bool                 // Check if CSRF token exists
$form->getCsrfTokenName(): string           // Get token field name
$form->getCsrfTokenValue(): ?string         // Get token value
```

#### Form Data
```php
$form->values(array $values): self          // Set pre-filled values
$form->getValues(): array                   // Get all values
$form->setErrors(array $errors): self       // Set validation errors
$form->getErrors(): array                   // Get validation errors
```

#### Validation & Rendering
```php
$form->validate(array $data): bool          // Validate form data
$form->render(): string                     // Render form HTML
$form->toJson(): string                     // Export form as JSON
```

#### Getters
```php
$form->getName(): string                    // Get form name
$form->getMethod(): string                  // Get HTTP method
$form->getAction(): string                  // Get form action
$form->getTheme(): string                   // Get theme name
$form->getAttributes(): array               // Get HTML attributes
```

---

## Field Base Class

### Constructor
```php
new Field(string $name, string $type)
```

### Methods

#### Field Configuration
```php
$field->label(string $label): self          // Set field label
$field->value(mixed $value): self           // Set field value
$field->placeholder(string $text): self     // Set placeholder text
$field->help(string $text): self            // Set help text
$field->required(bool $required = true): self // Mark as required
```

#### Validation
```php
$field->rule(string $rule): self            // Add validation rule
$field->rules(array $rules): self           // Add multiple rules
```

#### HTML Attributes
```php
$field->attributes(array $attrs): self      // Set custom attributes
$field->addClass(string $class): self       // Add CSS class
$field->id(string $id): self                // Set field ID
```

#### Getters
```php
$field->getName(): string                   // Get field name
$field->getType(): string                   // Get field type
$field->getLabel(): string                  // Get field label
$field->getValue(): mixed                   // Get field value
$field->getPlaceholder(): string            // Get placeholder
$field->getHelpText(): string               // Get help text
$field->isRequired(): bool                  // Check if required
$field->getRules(): array                   // Get validation rules
$field->getAttributes(): array              // Get HTML attributes
$field->toArray(): array                    // Convert to array
```

---

## Field Types

### TextField
```php
new TextField(string $name, string $inputType = 'text')
$field->getInputType(): string
```

### EmailField
```php
new EmailField(string $name)
// Extends TextField with email validation
```

### PasswordField
```php
new PasswordField(string $name)
// Extends TextField with password input type
```

### NumberField
```php
new NumberField(string $name)
// Extends TextField with numeric validation
```

### PhoneField
```php
new PhoneField(string $name)
// Extends TextField with phone validation
```

### URLField
```php
new URLField(string $name)
// Extends TextField with URL validation
```

### DateField, TimeField, DateTimeField
```php
new DateField(string $name)
new TimeField(string $name)
new DateTimeField(string $name)
// Extend TextField with date/time validation
```

### TextAreaField
```php
new TextAreaField(string $name)
$field->rows(int $rows): self
$field->cols(int $cols): self
$field->getRows(): int
$field->getCols(): int
```

### SelectField
```php
new SelectField(string $name)
$field->options(array $options): self
$field->multiple(bool $multiple = true): self
$field->getOptions(): array
$field->isMultiple(): bool
```

### CheckboxField
```php
new CheckboxField(string $name)
$field->inline(bool $inline = true): self
$field->isInline(): bool
```

### RadioField
```php
new RadioField(string $name)
$field->options(array $options): self
$field->inline(bool $inline = true): self
$field->getOptions(): array
$field->isInline(): bool
```

### FileField
```php
new FileField(string $name)
$field->accept(array $types): self
$field->multiple(bool $multiple = true): self
$field->getAcceptedTypes(): array
$field->isMultiple(): bool
```

### HiddenField
```php
new HiddenField(string $name)
```

### SubmitField
```php
new SubmitField(string $name = 'submit', string $label = 'Submit')
```

### ResetField
```php
new ResetField(string $name = 'reset', string $label = 'Reset')
```

### ButtonField
```php
new ButtonField(string $name, string $label = 'Button')
```

---

## Validator Class

### Methods
```php
$validator->validate(array $data, array $rules): bool
$validator->getErrors(): array
$validator->registerRule(string $name, callable $callback): void
```

### Built-in Rules
- `required` - Field is required
- `email` - Valid email format
- `numeric` - Numeric value
- `phone` - Valid phone format
- `url` - Valid URL format
- `date` - Valid date format
- `datetime` - Valid datetime format
- `min:n` - Minimum length
- `max:n` - Maximum length
- `minvalue:n` - Minimum value
- `maxvalue:n` - Maximum value
- `match:field` - Match another field

---

## CsrfToken Class

### Constructor
```php
new CsrfToken(string $tokenName = '_token', int $length = 32)
```

### Methods
```php
$csrf->generate(): string                   // Generate new token
$csrf->getToken(): string                   // Get current token
$csrf->verify(string $token): bool          // Verify token
$csrf->verifyRequest(): bool                // Verify from $_POST
$csrf->regenerate(): string                 // Generate new token
```

---

## Renderer Class

### Constructor
```php
new Renderer(string $theme = 'bootstrap')
```

### Methods
```php
$renderer->renderForm(FormBuilder $form): string
$renderer->renderField(Field $field): string
$renderer->renderFieldInput(Field $field): string
$renderer->renderLabel(Field $field): string
$renderer->renderError(Field $field): string
$renderer->renderHelp(Field $field): string
```

---

## AjaxHandler Class

### Static Methods
```php
AjaxHandler::isAjaxRequest(): bool          // Check if AJAX request
```

### Constructor
```php
new AjaxHandler(FormBuilder $form)
```

### Methods
```php
$handler->validate(): bool                  // Validate form
$handler->setSuccess(bool $success): self   // Set success status
$handler->setMessage(string $msg): self     // Set response message
$handler->setData(array $data): self        // Set response data
$handler->setErrors(array $errors): self    // Set errors
$handler->send(): void                      // Send JSON response
```

---

## Laravel Integration

### Service Provider
```php
Tweekersnut\FormsLib\Laravel\FormsLibServiceProvider
```

### Facade
```php
use Tweekersnut\FormsLib\Laravel\Facades\FormsFacade as Forms;

Forms::create(string $name, string $theme): FormBuilder
```

### Helper Functions
```php
form(string $name, string $theme = 'bootstrap'): FormBuilder
form_field(string $type, string $name): Field
form_render(FormBuilder $form): string
```

### Configuration
```php
config('forms-lib.theme')           // Default theme
config('forms-lib.ajax.enabled')    // AJAX enabled
config('forms-lib.ajax.timeout')    // AJAX timeout
config('forms-lib.validation.show_errors')
config('forms-lib.validation.error_class')
```

---

## Examples

### Basic Form
```php
$form = new FormBuilder('contact', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field(new SubmitField('submit', 'Send'));
echo $form->render();
```

### With Validation
```php
if ($form->validate($_POST)) {
    // Process form
} else {
    $form->values($_POST);
    echo $form->render();
}
```

### With CSRF
```php
$csrf = new CsrfToken('_token');
$form->withCsrfToken($csrf->generate());
```

### Custom Validation
```php
$validator = new Validator();
$validator->registerRule('custom', function($value) {
    return strlen($value) > 5;
});
```

---

For more examples, see the `examples/` directory.

