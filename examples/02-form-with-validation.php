<?php
/**
 * Form with Validation Example - Core PHP
 * 
 * This example demonstrates form validation and error handling
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SubmitField;

// Create form
$form = new FormBuilder('contact_form', 'bootstrap');
$form->method('POST')
    ->action($_SERVER['PHP_SELF']);

// Add fields
$form->field(
    (new TextField('name'))
        ->label('Full Name')
        ->placeholder('John Doe')
        ->required()
        ->rule('min:2')
        ->rule('max:100')
);

$form->field(
    (new EmailField('email'))
        ->label('Email')
        ->placeholder('john@example.com')
        ->required()
);

$form->field(
    (new TextField('phone'))
        ->label('Phone Number')
        ->placeholder('+1 (555) 123-4567')
        ->rule('phone')
);

$form->field(
    (new TextAreaField('message'))
        ->label('Message')
        ->placeholder('Your message here...')
        ->rows(5)
        ->required()
        ->rule('min:10')
        ->rule('max:1000')
);

$form->field(new SubmitField('submit', 'Send Message'));

// Handle form submission
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form->validate($_POST)) {
        $success = true;
        // Process form data here
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'message' => $_POST['message'],
        ];
        // Save to database, send email, etc.
    } else {
        // Set form values and errors
        $form->values($_POST);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2>Contact Us</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Thank you! Your message has been sent successfully.
                    </div>
                <?php endif; ?>

                <?php echo $form->render(); ?>
            </div>
        </div>
    </div>
</body>
</html>

