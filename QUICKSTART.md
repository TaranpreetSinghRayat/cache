# Tweekersnut Forms Library - Quick Start Guide

## 5-Minute Setup

### Installation

```bash
composer require tweekersnut/forms-lib
```

### Core PHP - Hello World

```php
<?php
require_once 'vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\SubmitField;

// Create form
$form = new FormBuilder('hello_form', 'bootstrap');
$form->method('POST')->action('/submit');

// Add fields
$form->field((new TextField('name'))->label('Your Name')->required());
$form->field(new SubmitField('submit', 'Say Hello'));

// Render
echo $form->render();
?>
```

### Laravel - Hello World

```php
<?php
// In your controller
use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\SubmitField;

$form = new FormBuilder('hello_form', 'bootstrap');
$form->method('POST')
    ->action(route('submit'))
    ->withCsrfToken(csrf_token());

$form->field((new TextField('name'))->label('Your Name')->required());
$form->field(new SubmitField('submit', 'Say Hello'));

return view('form', ['form' => $form]);
?>
```

```blade
<!-- In your Blade template -->
{!! $form->render() !!}
```

---

## Common Tasks

### Create a Contact Form

```php
use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SubmitField;

$form = new FormBuilder('contact', 'bootstrap');
$form->method('POST')->action('/contact/submit');

$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field((new TextAreaField('message'))->label('Message')->required());
$form->field(new SubmitField('submit', 'Send'));

echo $form->render();
```

### Validate Form Data

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form->validate($_POST)) {
        // Form is valid - process data
        $name = $_POST['name'];
        $email = $_POST['email'];
        // Save to database, send email, etc.
    } else {
        // Form has errors
        $errors = $form->getErrors();
        $form->values($_POST); // Pre-fill form
        echo $form->render(); // Show form with errors
    }
}
```

### Add CSRF Protection

```php
session_start();

use Tweekersnut\FormsLib\Security\CsrfToken;

// Generate token
$csrf = new CsrfToken('_token');
$_SESSION['csrf_token'] = $csrf->generate();

// Add to form
$form->withCsrfToken($_SESSION['csrf_token']);

// Verify on submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$csrf->verify($_POST['_token'])) {
        die('Security token invalid');
    }
    // Process form
}
```

### Use Different Themes

```php
// Bootstrap (default)
$form = new FormBuilder('form', 'bootstrap');

// Tailwind CSS
$form = new FormBuilder('form', 'tailwind');
```

### Add Validation Rules

```php
$form->field(
    (new TextField('username'))
        ->label('Username')
        ->required()
        ->rule('min:3')
        ->rule('max:20')
);

$form->field(
    (new TextField('age'))
        ->label('Age')
        ->rule('numeric')
        ->rule('minvalue:18')
        ->rule('maxvalue:120')
);

$form->field(
    (new TextField('website'))
        ->label('Website')
        ->rule('url')
);
```

### Pre-fill Form Values

```php
$form->values([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'message' => 'Hello!'
]);

echo $form->render(); // Form will have pre-filled values
```

### Add Custom Attributes

```php
$form->field(
    (new TextField('name'))
        ->label('Name')
        ->required()
        ->attributes(['data-custom' => 'value'])
        ->addClass('custom-class')
        ->id('custom-id')
);
```

### Handle AJAX Submission

```php
use Tweekersnut\FormsLib\AJAX\AjaxHandler;

if (AjaxHandler::isAjaxRequest()) {
    $handler = new AjaxHandler($form);
    
    if ($handler->validate()) {
        $handler->setSuccess(true)
                ->setMessage('Form submitted successfully!')
                ->send();
    } else {
        $handler->setSuccess(false)
                ->setErrors($form->getErrors())
                ->send();
    }
}
```

---

## Field Types

| Field Type | Usage | Example |
|-----------|-------|---------|
| TextField | Text input | `new TextField('name')` |
| EmailField | Email input | `new EmailField('email')` |
| PasswordField | Password input | `new PasswordField('password')` |
| NumberField | Number input | `new NumberField('age')` |
| PhoneField | Phone input | `new PhoneField('phone')` |
| URLField | URL input | `new URLField('website')` |
| DateField | Date input | `new DateField('birthdate')` |
| TimeField | Time input | `new TimeField('time')` |
| TextAreaField | Multi-line text | `new TextAreaField('message')` |
| SelectField | Dropdown | `new SelectField('country')` |
| CheckboxField | Checkbox | `new CheckboxField('agree')` |
| RadioField | Radio button | `new RadioField('option')` |
| FileField | File upload | `new FileField('document')` |
| HiddenField | Hidden field | `new HiddenField('token')` |
| SubmitField | Submit button | `new SubmitField('submit')` |

---

## Validation Rules

| Rule | Description | Example |
|------|-------------|---------|
| required | Field is required | `->rule('required')` |
| email | Valid email format | `->rule('email')` |
| numeric | Numeric value | `->rule('numeric')` |
| phone | Valid phone format | `->rule('phone')` |
| url | Valid URL format | `->rule('url')` |
| date | Valid date format | `->rule('date')` |
| min:n | Minimum length | `->rule('min:3')` |
| max:n | Maximum length | `->rule('max:50')` |
| minvalue:n | Minimum value | `->rule('minvalue:18')` |
| maxvalue:n | Maximum value | `->rule('maxvalue:100')` |
| match:field | Match another field | `->rule('match:password')` |

---

## Next Steps

1. **Read Full Documentation**: See `README.md` for complete documentation
2. **View Examples**: Check `examples/` directory for working examples
3. **Integration Guide**: See `INTEGRATION_GUIDE.md` for detailed integration instructions
4. **Run Tests**: Execute `php run-all-tests.php` to see all features in action

---

## Need Help?

- Check `examples/` directory for working code
- Read `INTEGRATION_GUIDE.md` for detailed instructions
- Review test files in root directory for usage patterns
- Check `README.md` for API documentation

Happy coding! 🚀

