<?php
/**
 * AJAX Form Example - Core PHP
 * 
 * This example demonstrates AJAX form submission with validation
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\SelectField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\AJAX\AjaxHandler;

// Handle AJAX requests
if (AjaxHandler::isAjaxRequest()) {
    $form = new FormBuilder('subscription_form', 'bootstrap');
    
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
        (new SelectField('plan'))
            ->label('Plan')
            ->options([
                'free' => 'Free Plan',
                'pro' => 'Pro Plan',
                'enterprise' => 'Enterprise Plan',
            ])
            ->required()
    );

    $form->field(new SubmitField('submit', 'Subscribe'));

    $handler = new AjaxHandler($form);
    
    if ($handler->validate()) {
        // Process subscription
        $data = $handler->getFormData();
        
        // Simulate processing
        $handler->setSuccess(true)
            ->setMessage('Successfully subscribed!')
            ->setData([
                'subscription_id' => uniqid('sub_'),
                'plan' => $data['plan'],
            ]);
    }

    $handler->send();
}

// Render form page
$form = new FormBuilder('subscription_form', 'bootstrap');

$form->field(
    (new TextField('name'))
        ->label('Name')
        ->placeholder('John Doe')
        ->required()
        ->rule('min:2')
);

$form->field(
    (new EmailField('email'))
        ->label('Email')
        ->placeholder('john@example.com')
        ->required()
);

$form->field(
    (new SelectField('plan'))
        ->label('Select Plan')
        ->options([
            'free' => 'Free Plan - $0/month',
            'pro' => 'Pro Plan - $29/month',
            'enterprise' => 'Enterprise Plan - Custom',
        ])
        ->required()
);

$form->field(new SubmitField('submit', 'Subscribe Now'));

?>
<!DOCTYPE html>
<html>
<head>
    <title>Subscription Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .is-loading { opacity: 0.6; pointer-events: none; }
        .has-error .form-control { border-color: #dc3545; }
        .error-message { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Subscribe Now</h2>
                <div id="successMessage" class="alert alert-success d-none"></div>
                
                <?php echo $form->render(); ?>
            </div>
        </div>
    </div>

    <script src="../src/AJAX/form-handler.js"></script>
    <script>
        const formHandler = new FormHandler('#subscription_form', {
            submitUrl: '<?php echo $_SERVER['PHP_SELF']; ?>',
            onSuccess: function(response) {
                document.getElementById('successMessage').textContent = response.message;
                document.getElementById('successMessage').classList.remove('d-none');
                formHandler.reset();
            },
            onError: function(response) {
                console.error('Form error:', response);
            }
        });
    </script>
</body>
</html>

