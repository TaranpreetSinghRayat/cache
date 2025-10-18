<?php
/**
 * Core PHP Integration Example
 * 
 * This example demonstrates how to use Tweekersnut Forms Library
 * in a standalone Core PHP application without any framework.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SelectField;
use Tweekersnut\FormsLib\Fields\CheckboxField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\Security\CsrfToken;

// Start session for CSRF token storage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $csrf = new CsrfToken('_token');
    $_SESSION['csrf_token'] = $csrf->generate();
}

// Create form
$form = new FormBuilder('user_registration', 'bootstrap');
$form->method('POST')
    ->action($_SERVER['PHP_SELF'])
    ->withCsrfToken($_SESSION['csrf_token']);

// Add fields
$form->field(
    (new TextField('username'))
        ->label('Username')
        ->placeholder('Choose a username')
        ->required()
        ->rule('min:3')
        ->rule('max:20')
);

$form->field(
    (new EmailField('email'))
        ->label('Email Address')
        ->placeholder('your@email.com')
        ->required()
);

$form->field(
    (new TextField('password'))
        ->label('Password')
        ->placeholder('Enter a strong password')
        ->required()
        ->rule('min:8')
);

$form->field(
    (new SelectField('country'))
        ->label('Country')
        ->required()
);

$form->field(
    (new CheckboxField('terms'))
        ->label('I agree to the Terms and Conditions')
        ->required()
);

$form->field(new SubmitField('submit', 'Register'));

// Handle form submission
$success = false;
$errors = [];
$submittedData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $csrf = new CsrfToken('_token');
    if (!isset($_POST['_token']) || !$csrf->verify($_POST['_token'])) {
        $errors['csrf'] = 'Invalid security token. Please try again.';
    } else {
        // Validate form
        if ($form->validate($_POST)) {
            $success = true;
            $submittedData = $_POST;
            
            // Here you would typically:
            // 1. Sanitize the data
            // 2. Save to database
            // 3. Send confirmation email
            // 4. Redirect to success page
            
            // Regenerate CSRF token after successful submission
            $_SESSION['csrf_token'] = $csrf->regenerate();
        } else {
            $errors = $form->getErrors();
            $form->values($_POST);
        }
    }
}

// Render output
if (php_sapi_name() === 'cli') {
    // CLI mode
    echo "=== Core PHP Registration Form ===\n";
    echo $form->render();
} else {
    // Web mode
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>User Registration - Core PHP</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2>User Registration</h2>
                    <p class="text-muted">Create your account</p>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <h4>Registration Successful!</h4>
                            <p>Welcome, <?php echo htmlspecialchars($submittedData['username']); ?>!</p>
                            <p>A confirmation email has been sent to <?php echo htmlspecialchars($submittedData['email']); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors) && !$success): ?>
                        <div class="alert alert-danger">
                            <h4>Registration Failed</h4>
                            <ul>
                                <?php foreach ($errors as $field => $fieldErrors): ?>
                                    <?php foreach ($fieldErrors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php echo $form->render(); ?>

                    <hr>
                    <h5>Features Demonstrated:</h5>
                    <ul>
                        <li>✓ Form creation with multiple field types</li>
                        <li>✓ CSRF token protection</li>
                        <li>✓ Form validation</li>
                        <li>✓ Error handling and display</li>
                        <li>✓ Pre-filled form values on validation failure</li>
                        <li>✓ Bootstrap styling</li>
                    </ul>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}

