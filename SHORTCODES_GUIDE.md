# Tweekersnut Forms Library - Shortcodes Guide

## Overview

The Shortcodes system allows you to create reusable forms that can be embedded in forum posts, pages, or any content using simple shortcode syntax. Perfect for forums, CMS platforms, and content management systems.

## Quick Start

### 1. Create a Form Shortcode

```php
use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\Shortcodes\FormShortcode;

// Create form
$form = new FormBuilder('contact_form', 'bootstrap');
$form->method('POST')->action('/submit');
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field(new SubmitField('submit', 'Send'));

// Create shortcode
$shortcode = new FormShortcode($form);

// Register shortcode
$shortcode->registerShortcode('contact_form');
```

### 2. Use in Content

```php
// In your forum/page content
$content = "
    <h1>Contact Us</h1>
    <p>Fill out the form below:</p>
    [contact_form]
";

// Parse and render
echo ShortcodeManager::parse($content);
```

## Features

### Success Callback

Handle successful form submissions:

```php
$shortcode->onSuccess(function ($data, $shortcode) {
    // $data contains submitted form data
    // Save to database, send email, etc.
    
    // Return custom data (optional)
    return ['ticket_id' => 12345];
});
```

### Failure Callback

Handle validation failures:

```php
$shortcode->onFail(function ($errors, $shortcode) {
    // $errors contains validation errors
    // Log errors, send notification, etc.
    
    // Return custom data (optional)
    return null;
});
```

### Before Render Callback

Modify form before rendering:

```php
$shortcode->beforeRender(function ($form, $attributes) {
    // Modify form based on attributes
    // Pre-fill fields, add fields dynamically, etc.
});
```

### After Render Callback

Modify rendered HTML:

```php
$shortcode->afterRender(function ($html, $form, $attributes) {
    // Wrap HTML, add custom styling, etc.
    return '<div class="custom-wrapper">' . $html . '</div>';
});
```

### Custom Data

Store and retrieve custom data:

```php
$shortcode->setData([
    'user_id' => 123,
    'forum_id' => 456,
    'category' => 'support'
]);

// Later retrieve
$userId = $shortcode->getData('user_id');
$allData = $shortcode->getData();
```

## Shortcode Syntax

### Basic Shortcode

```
[contact_form]
```

### Shortcode with Attributes

```
[support_ticket priority="high" category="billing"]
```

### Attribute Rules

- Attribute names: alphanumeric and underscores
- Attribute values: enclosed in double quotes
- Multiple attributes separated by spaces

## ShortcodeManager API

### Register Shortcode

```php
ShortcodeManager::register('form_name', function ($attributes) {
    // Return HTML
});
```

### Execute Shortcode

```php
$html = ShortcodeManager::execute('form_name', ['param' => 'value']);
```

### Parse Content

```php
$html = ShortcodeManager::parse($content);
```

### Check if Exists

```php
if (ShortcodeManager::exists('form_name')) {
    // ...
}
```

### Get All Shortcodes

```php
$shortcodes = ShortcodeManager::getAll();
```

### Unregister Shortcode

```php
ShortcodeManager::unregister('form_name');
```

### Clear All Shortcodes

```php
ShortcodeManager::clear();
```

## Use Cases

### 1. Forum Contact Form

```php
$form = new FormBuilder('forum_contact', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field((new TextAreaField('message'))->label('Message')->required());

$shortcode = new FormShortcode($form);
$shortcode->onSuccess(function ($data) {
    // Send email to forum admin
    Mail::send('emails.contact', $data, function ($m) {
        $m->to('admin@forum.com');
    });
});
$shortcode->registerShortcode('forum_contact');
```

### 2. Support Ticket System

```php
$form = new FormBuilder('support_ticket', 'bootstrap');
$form->field((new TextField('title'))->label('Issue Title')->required());
$form->field((new SelectField('priority'))->label('Priority')->options([
    'low' => 'Low',
    'medium' => 'Medium',
    'high' => 'High'
]));
$form->field((new TextAreaField('description'))->label('Description')->required());

$shortcode = new FormShortcode($form);
$shortcode->onSuccess(function ($data) {
    // Create support ticket in database
    $ticket = SupportTicket::create($data);
    return ['ticket_id' => $ticket->id];
});
$shortcode->registerShortcode('support_ticket');
```

### 3. Newsletter Signup

```php
$form = new FormBuilder('newsletter', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());

$shortcode = new FormShortcode($form);
$shortcode->onSuccess(function ($data) {
    // Subscribe to newsletter
    Newsletter::subscribe($data['email'], $data['name']);
});
$shortcode->registerShortcode('newsletter');
```

### 4. Forum Post Creation

```php
$form = new FormBuilder('create_post', 'bootstrap');
$form->field((new TextField('title'))->label('Title')->required());
$form->field((new TextAreaField('content'))->label('Content')->required());

$shortcode = new FormShortcode($form);
$shortcode->onSuccess(function ($data) {
    // Create forum post
    $post = ForumPost::create($data);
    return ['post_id' => $post->id];
});
$shortcode->registerShortcode('create_post');
```

## Laravel Integration

### 1. Register Service Provider

Add to `config/app.php`:

```php
'providers' => [
    // ...
    Tweekersnut\FormsLib\Laravel\ShortcodeServiceProvider::class,
],
```

### 2. Use Blade Directives

```blade
<!-- Execute single shortcode -->
@shortcode('contact_form')

<!-- Parse content with shortcodes -->
@shortcodes($pageContent)
```

### 3. Register in Config

Create `config/forms-lib.php`:

```php
return [
    'shortcodes' => [
        'contact_form' => function ($attributes) {
            // Return form HTML
        },
        'support_ticket' => function ($attributes) {
            // Return form HTML
        },
    ],
];
```

## Best Practices

### 1. Use Meaningful Names

```php
// Good
$shortcode->registerShortcode('contact_form');
$shortcode->registerShortcode('support_ticket');

// Avoid
$shortcode->registerShortcode('form1');
$shortcode->registerShortcode('f');
```

### 2. Always Validate

```php
$shortcode->onSuccess(function ($data) {
    // Validate data before processing
    if (empty($data['email'])) {
        throw new Exception('Email is required');
    }
});
```

### 3. Handle Errors Gracefully

```php
$shortcode->onFail(function ($errors) {
    // Log errors
    Log::warning('Form validation failed', $errors);
});
```

### 4. Use Callbacks for Side Effects

```php
$shortcode->onSuccess(function ($data) {
    // Send emails
    // Create database records
    // Trigger webhooks
    // Update caches
});
```

### 5. Sanitize Output

```php
$shortcode->afterRender(function ($html) {
    // Sanitize HTML if needed
    return htmlspecialchars($html);
});
```

## Examples

See `examples/07-shortcode-forms.php` for complete working examples.

## Troubleshooting

### Shortcode not rendering

- Check if shortcode is registered: `ShortcodeManager::exists('name')`
- Verify shortcode syntax: `[name]` or `[name attr="value"]`
- Check for typos in shortcode name

### Form not validating

- Ensure validation rules are added to fields
- Check if form data is being submitted correctly
- Verify CSRF token if required

### Callbacks not executing

- Ensure callbacks are registered before rendering
- Check if form validation passes/fails as expected
- Verify callback logic

## Support

For issues or questions, refer to the main README.md or INTEGRATION_GUIDE.md.

License: MIT

