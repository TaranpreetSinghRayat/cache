<?php
/**
 * Basic Form Example - Core PHP
 * 
 * This example demonstrates how to create and render a basic form
 * using the Forms Library in core PHP.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\PasswordField;
use Tweekersnut\FormsLib\Fields\SubmitField;

// Create a new form
$form = new FormBuilder('login_form', 'bootstrap');
$form->method('POST')
    ->action('/login')
    ->attributes(['class' => 'login-form', 'id' => 'loginForm']);

// Add fields
$form->field(
    (new TextField('username'))
        ->label('Username')
        ->placeholder('Enter your username')
        ->required()
        ->rule('min:3')
        ->rule('max:50')
);

$form->field(
    (new EmailField('email'))
        ->label('Email Address')
        ->placeholder('your@email.com')
        ->required()
);

$form->field(
    (new PasswordField('password'))
        ->label('Password')
        ->placeholder('Enter your password')
        ->required()
        ->rule('min:8')
);

$form->field(
    new SubmitField('submit', 'Login')
);

// Render the form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Login</h2>
                <?php echo $form->render(); ?>
            </div>
        </div>
    </div>
</body>
</html>

