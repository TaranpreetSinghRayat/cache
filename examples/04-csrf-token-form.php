<?php
/**
 * CSRF Token Form Example - Core PHP
 * 
 * This example demonstrates how to use CSRF token protection with forms
 */

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\Security\CsrfToken;

// Initialize CSRF token
$csrf = new CsrfToken('_token');
$csrfToken = $csrf->getToken();

// Handle form submission
$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!$csrf->verifyRequest()) {
        $message = 'Invalid CSRF token. Please try again.';
    } else {
        // Create form for validation
        $form = new FormBuilder('secure_form', 'bootstrap');
        
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

        $form->field(
            (new TextAreaField('message'))
                ->label('Message')
                ->required()
                ->rule('min:10')
        );

        // Validate form data
        if ($form->validate($_POST)) {
            $success = true;
            $message = 'Form submitted successfully!';
            // Process form data here
        } else {
            $message = 'Please fix the errors below.';
        }
    }
}

// Create form
$form = new FormBuilder('secure_form', 'bootstrap');
$form->method('POST')
    ->action($_SERVER['PHP_SELF'])
    ->withCsrfToken($csrfToken); // Add CSRF token

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
        ->label('Email Address')
        ->placeholder('john@example.com')
        ->required()
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

$form->field(new SubmitField('submit', 'Send Secure Message'));

// Set form values if there were errors
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$success) {
    $form->values($_POST);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Form with CSRF Protection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2>Secure Contact Form</h2>
                <p class="text-muted">This form is protected with CSRF token</p>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php echo $form->render(); ?>

                <hr>
                <h5>How CSRF Protection Works:</h5>
                <ul>
                    <li>A unique token is generated and stored in the session</li>
                    <li>The token is included as a hidden field in the form</li>
                    <li>When the form is submitted, the token is verified</li>
                    <li>If the token is invalid or missing, the request is rejected</li>
                    <li>This prevents Cross-Site Request Forgery attacks</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

