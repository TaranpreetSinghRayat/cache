# Tweekersnut Forms Library - Integration Guide

## Table of Contents
1. [Core PHP Integration](#core-php-integration)
2. [Laravel Integration](#laravel-integration)
3. [Common Use Cases](#common-use-cases)
4. [Best Practices](#best-practices)

---

## Core PHP Integration

### Installation

```bash
composer require tweekersnut/forms-lib
```

### Basic Setup

```php
<?php
require_once 'vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\SubmitField;

// Create a form
$form = new FormBuilder('contact_form', 'bootstrap');
$form->method('POST')->action('/submit');

// Add fields
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field(new SubmitField('submit', 'Send'));

// Render
echo $form->render();
?>
```

### With CSRF Protection

```php
<?php
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
        die('CSRF token verification failed');
    }
    // Process form
}
?>
```

### Form Validation

```php
<?php
$form = new FormBuilder('registration', 'bootstrap');
$form->field((new TextField('username'))->required()->rule('min:3')->rule('max:20'));
$form->field((new EmailField('email'))->required());

// Validate
if ($form->validate($_POST)) {
    // Form is valid
    $data = $_POST;
} else {
    // Show errors
    $errors = $form->getErrors();
    $form->values($_POST); // Pre-fill form
}
?>
```

### Complete Example

See `examples/05-core-php-integration.php` for a complete working example.

---

## Laravel Integration

### Installation

```bash
composer require tweekersnut/forms-lib
```

### Configuration

1. Add to `config/app.php`:

```php
'providers' => [
    // ...
    Tweekersnut\FormsLib\Laravel\FormsLibServiceProvider::class,
],

'aliases' => [
    // ...
    'Forms' => Tweekersnut\FormsLib\Laravel\Facades\FormsFacade::class,
],
```

2. Publish configuration:

```bash
php artisan vendor:publish --provider="Tweekersnut\FormsLib\Laravel\FormsLibServiceProvider"
```

### In Controllers

```php
<?php
namespace App\Http\Controllers;

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\SubmitField;

class UserController extends Controller
{
    public function create()
    {
        $form = new FormBuilder('user_form', 'bootstrap');
        $form->method('POST')
            ->action(route('users.store'))
            ->withCsrfToken(csrf_token());

        $form->field((new TextField('name'))->label('Name')->required());
        $form->field((new EmailField('email'))->label('Email')->required());
        $form->field(new SubmitField('submit', 'Create'));

        return view('users.create', ['form' => $form]);
    }

    public function store(Request $request)
    {
        $form = new FormBuilder('user_form', 'bootstrap');
        $form->field((new TextField('name'))->label('Name')->required());
        $form->field((new EmailField('email'))->label('Email')->required());

        if (!$form->validate($request->all())) {
            return back()->withErrors($form->getErrors())->withInput();
        }

        User::create($request->all());
        return redirect()->route('users.index')->with('success', 'User created!');
    }
}
?>
```

### In Blade Templates

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! $form->render() !!}
</div>
@endsection
```

### Using Helper Functions

```php
<?php
// In controller or view
$form = form('contact_form', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
echo $form->render();
?>
```

### Using Facade

```php
<?php
use Tweekersnut\FormsLib\Laravel\Facades\FormsFacade as Forms;

$form = Forms::create('contact_form', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
?>
```

### Complete Example

See `examples/06-laravel-integration.php` for a complete working example.

---

## Common Use Cases

### 1. Contact Form

```php
$form = new FormBuilder('contact', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field((new TextAreaField('message'))->label('Message')->required()->rule('min:10'));
$form->field(new SubmitField('submit', 'Send'));
```

### 2. User Registration

```php
$form = new FormBuilder('register', 'bootstrap');
$form->field((new TextField('username'))->label('Username')->required()->rule('min:3'));
$form->field((new EmailField('email'))->label('Email')->required());
$form->field((new TextField('password'))->label('Password')->required()->rule('min:8'));
$form->field((new CheckboxField('terms'))->label('I agree to terms')->required());
$form->field(new SubmitField('submit', 'Register'));
```

### 3. Search Form

```php
$form = new FormBuilder('search', 'bootstrap');
$form->method('GET')->action('/search');
$form->field((new TextField('q'))->label('Search')->placeholder('Enter search term'));
$form->field((new SelectField('category'))->label('Category'));
$form->field(new SubmitField('submit', 'Search'));
```

### 4. AJAX Form

```php
$form = new FormBuilder('ajax_form', 'bootstrap');
$form->attributes(['data-ajax' => 'true']);
$form->field((new TextField('name'))->label('Name')->required());
$form->field(new SubmitField('submit', 'Submit'));

// JavaScript will handle AJAX submission
```

---

## Best Practices

### 1. Always Validate

```php
if (!$form->validate($_POST)) {
    $form->values($_POST); // Pre-fill with submitted data
    // Show form with errors
}
```

### 2. Use CSRF Protection

```php
$form->withCsrfToken(csrf_token()); // Laravel
$form->withCsrfToken($_SESSION['csrf_token']); // Core PHP
```

### 3. Sanitize Input

```php
$data = array_map('htmlspecialchars', $_POST);
// Or use Laravel's built-in sanitization
```

### 4. Use Appropriate Field Types

```php
// Good
$form->field((new EmailField('email'))->label('Email'));
$form->field((new TextField('phone'))->label('Phone')->rule('phone'));

// Avoid
$form->field((new TextField('email'))->label('Email'));
```

### 5. Add Helpful Validation Rules

```php
$form->field(
    (new TextField('username'))
        ->label('Username')
        ->required()
        ->rule('min:3')
        ->rule('max:20')
        ->help('3-20 characters, alphanumeric only')
);
```

### 6. Handle Errors Gracefully

```php
if ($form->validate($data)) {
    // Success
} else {
    $errors = $form->getErrors();
    // Log errors
    // Show user-friendly messages
}
```

### 7. Use Appropriate Themes

```php
// Bootstrap (default)
$form = new FormBuilder('form', 'bootstrap');

// Tailwind
$form = new FormBuilder('form', 'tailwind');
```

---

## Troubleshooting

### Form not rendering
- Check if templates directory exists: `src/Rendering/Templates/{theme}/`
- Verify theme name is correct: 'bootstrap' or 'tailwind'

### Validation not working
- Ensure fields have validation rules: `->rule('required')`
- Check if `validate()` is called with correct data array

### CSRF token errors
- Ensure session is started before generating token
- Verify token is included in form submission
- Check token verification logic

### AJAX not working
- Include `src/AJAX/form-handler.js` in your HTML
- Add `data-ajax="true"` to form attributes
- Ensure backend returns proper JSON response

---

## Support

For issues, questions, or contributions, please visit the GitHub repository.

License: MIT

