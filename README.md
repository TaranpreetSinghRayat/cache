# Forms Library - PHP

A comprehensive, flexible PHP forms library for Laravel and Core PHP with Bootstrap/Tailwind CSS support, AJAX handling, dynamic validation, and CSRF token protection.

## Features

- ✨ **Multiple Field Types** - Text, Email, Password, Textarea, Select, Checkbox, Radio, File, Hidden, and more
- 🎨 **CSS Framework Support** - Bootstrap 5 and Tailwind CSS with easy theme switching
- ✅ **Built-in Validation** - Email, URL, phone, date, numeric, and custom validators
- 🔄 **AJAX Support** - Complete AJAX form handling with real-time validation
- 🛡️ **CSRF Protection** - Built-in CSRF token generation and verification
- 🚀 **Laravel Integration** - Service provider, facades, and helper functions
- 📦 **Core PHP Compatible** - Works standalone without any framework
- 🎯 **Customizable** - Custom attributes, classes, IDs, and validation rules
- 📱 **Responsive** - Mobile-friendly form rendering
- 🏷️ **Shortcode System** - Create reusable forms for forums, CMS, and content platforms with callbacks

## Installation

```bash
composer require augment/forms-lib
```

### Laravel Setup

Add to your `config/app.php` providers:

```php
'providers' => [
    // ...
    Augment\FormsLib\Laravel\FormsLibServiceProvider::class,
],

'aliases' => [
    // ...
    'Forms' => Augment\FormsLib\Laravel\Facades\FormsFacade::class,
]
```

Publish configuration:

```bash
php artisan vendor:publish --tag=forms-lib-config
```

## Quick Start

### Core PHP

```php
use Augment\FormsLib\Core\FormBuilder;
use Augment\FormsLib\Fields\TextField;
use Augment\FormsLib\Fields\EmailField;
use Augment\FormsLib\Fields\SubmitField;

$form = new FormBuilder('contact_form', 'bootstrap');
$form->method('POST')->action('/submit');

$form->field(
    (new TextField('name'))
        ->label('Name')
        ->required()
        ->rule('min:2')
);

$form->field(
    (new EmailField('email'))
        ->label('Email')
        ->required()
);

$form->field(new SubmitField('submit', 'Send'));

echo $form->render();
```

### Laravel

```php
use Augment\FormsLib\Laravel\Facades\Forms;

$form = Forms::create('contact_form', 'bootstrap');
$form->method('POST')->action('/submit');

// Add fields...

echo $form->render();
```

Or use the helper:

```php
$form = form('contact_form');
```

## Field Types

- `TextField` - Text input with optional type (text, email, password, number, etc.)
- `EmailField` - Email input with validation
- `PasswordField` - Password input
- `NumberField` - Number input
- `PhoneField` - Phone number input
- `URLField` - URL input
- `DateField` - Date input
- `TimeField` - Time input
- `DateTimeField` - DateTime input
- `TextAreaField` - Textarea with configurable rows/cols
- `SelectField` - Dropdown select
- `CheckboxField` - Checkbox group
- `RadioField` - Radio button group
- `FileField` - File upload
- `HiddenField` - Hidden input
- `SubmitField` - Submit button
- `ResetField` - Reset button
- `ButtonField` - Generic button

## Validation Rules

Built-in validators:
- `required` - Field is required
- `email` - Valid email format
- `numeric` - Numeric value
- `phone` - Valid phone number
- `url` - Valid URL
- `date` - Valid date
- `datetime` - Valid datetime
- `min:n` - Minimum length
- `max:n` - Maximum length
- `minvalue:n` - Minimum numeric value
- `maxvalue:n` - Maximum numeric value
- `match:value` - Match specific value

### Custom Validation

```php
$validator = new Validator();
$validator->registerRule('custom_rule', function($value, $param, $field) {
    return strlen($value) > 5;
});
```

## AJAX Handling

### PHP Backend

```php
use Augment\FormsLib\AJAX\AjaxHandler;

if (AjaxHandler::isAjaxRequest()) {
    $handler = new AjaxHandler($form);
    
    if ($handler->validate()) {
        $handler->setSuccess(true)
            ->setMessage('Form submitted successfully')
            ->setData(['id' => 123]);
    }
    
    $handler->send();
}
```

### JavaScript Frontend

```html
<script src="form-handler.js"></script>
<script>
    const formHandler = new FormHandler('#myForm', {
        submitUrl: '/submit',
        onSuccess: function(response) {
            console.log('Success:', response);
        },
        onError: function(response) {
            console.log('Error:', response);
        }
    });
</script>
```

## CSRF Token Protection

Protect your forms from Cross-Site Request Forgery attacks:

```php
use Tweekersnut\FormsLib\Security\CsrfToken;

session_start();

// Initialize CSRF token
$csrf = new CsrfToken('_token');
$token = $csrf->getToken();

// Add token to form
$form->withCsrfToken($token);

// Verify token on submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrf->verifyRequest()) {
        die('Invalid CSRF token');
    }
    // Process form...
}
```

### CSRF Token Methods

```php
$csrf = new CsrfToken($tokenName);

$csrf->generate()           // Generate new token
$csrf->getToken()           // Get current token
$csrf->getTokenName()       // Get token field name
$csrf->verify($token)       // Verify specific token
$csrf->verifyRequest()      // Verify token from POST/GET
$csrf->regenerate()         // Regenerate token (after login)
$csrf->clear()              // Clear token
$csrf->setTokenLength($len) // Set token length
```

## Themes

### Bootstrap 5

```php
$form = new FormBuilder('myform', 'bootstrap');
```

### Tailwind CSS

```php
$form = new FormBuilder('myform', 'tailwind');
```

## Examples

See the `examples/` directory for complete working examples:
- `01-basic-form.php` - Basic form creation
- `02-form-with-validation.php` - Form validation
- `03-ajax-form.php` - AJAX form submission
- `04-csrf-token-form.php` - CSRF token protection

## API Reference

### FormBuilder

```php
$form = new FormBuilder($name, $theme);

$form->method($method)           // Set HTTP method
$form->action($url)              // Set form action
$form->attributes($attrs)        // Add HTML attributes
$form->field($field)             // Add field
$form->values($data)             // Set field values
$form->errors($errors)           // Set validation errors
$form->validate($data)           // Validate data
$form->render()                  // Render complete form
$form->renderField($name)        // Render single field
$form->toArray()                 // Convert to array
$form->toJson()                  // Convert to JSON
```

### Field

```php
$field->label($text)             // Set label
$field->value($value)            // Set value
$field->placeholder($text)       // Set placeholder
$field->help($text)              // Set help text
$field->required($bool)          // Mark as required
$field->rule($rule)              // Add validation rule
$field->rules($array)            // Add multiple rules
$field->attributes($attrs)       // Add HTML attributes
$field->addClass($class)         // Add CSS class
$field->id($id)                  // Set element ID
```

## Shortcodes (Forums & CMS)

Create reusable forms that can be embedded in forum posts and CMS content using simple shortcode syntax:

```php
use Tweekersnut\FormsLib\Shortcodes\FormShortcode;

// Create and configure form
$form = new FormBuilder('contact_form', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());

// Create shortcode
$shortcode = new FormShortcode($form);

// Add success callback
$shortcode->onSuccess(function ($data, $shortcode) {
    // Save to database, send email, etc.
    return ['success' => true];
});

// Register shortcode
$shortcode->registerShortcode('contact_form');
```

Use in content:
```
[contact_form]
[contact_form wrapper="custom-class"]
```

See `SHORTCODES_GUIDE.md` for complete documentation and examples.

## Documentation

- **[QUICKSTART.md](QUICKSTART.md)** - 5-minute quick start guide
- **[INTEGRATION_GUIDE.md](INTEGRATION_GUIDE.md)** - Detailed integration instructions for Core PHP and Laravel
- **[API_REFERENCE.md](API_REFERENCE.md)** - Complete API reference
- **[SHORTCODES_GUIDE.md](SHORTCODES_GUIDE.md)** - Shortcode system documentation
- **[examples/](examples/)** - Working examples for all features

## License

MIT License - see LICENSE file for details

## Support

For issues, questions, or contributions, please visit the repository.

