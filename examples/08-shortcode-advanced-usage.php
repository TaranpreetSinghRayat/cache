<?php
/**
 * Advanced Shortcode Usage Examples
 * 
 * This example demonstrates advanced features of the shortcode system:
 * - Custom callbacks
 * - Data passing
 * - Before/After render hooks
 * - Error handling
 * - Multiple forms on same page
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SelectField;
use Tweekersnut\FormsLib\Fields\CheckboxField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\Shortcodes\ShortcodeManager;
use Tweekersnut\FormsLib\Shortcodes\FormShortcode;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// EXAMPLE 1: Form with Custom Success Handler
// ============================================

$feedbackForm = new FormBuilder('user_feedback', 'bootstrap');
$feedbackForm->method('POST')->action($_SERVER['PHP_SELF']);
$feedbackForm->field((new TextField('name'))->label('Your Name')->required());
$feedbackForm->field((new EmailField('email'))->label('Email')->required());
$feedbackForm->field((new SelectField('rating'))
    ->label('How would you rate us?')
    ->required()
    ->options(['1' => '⭐ Poor', '2' => '⭐⭐ Fair', '3' => '⭐⭐⭐ Good', '4' => '⭐⭐⭐⭐ Very Good', '5' => '⭐⭐⭐⭐⭐ Excellent']));
$feedbackForm->field((new TextAreaField('feedback'))->label('Your Feedback')->required());

$feedbackShortcode = new FormShortcode($feedbackForm);

$feedbackShortcode->onSuccess(function ($data, $shortcode) {
    // Simulate saving to database
    $feedbackId = rand(1000, 9999);
    
    // Log feedback
    error_log("Feedback received from {$data['name']}: Rating {$data['rating']}/5");
    
    return [
        'feedback_id' => $feedbackId,
        'rating' => $data['rating'],
        'timestamp' => date('Y-m-d H:i:s')
    ];
});

$feedbackShortcode->beforeRender(function ($form, $attributes) {
    // Add custom CSS class based on attributes
    if (isset($attributes['style']) && $attributes['style'] === 'compact') {
        // Could modify form here
    }
});

$feedbackShortcode->afterRender(function ($html, $form, $attributes) {
    // Wrap form in custom container
    $wrapper = isset($attributes['wrapper']) ? htmlspecialchars($attributes['wrapper']) : 'feedback-form-wrapper';
    return '<div class="' . $wrapper . '">' . $html . '</div>';
});

$feedbackShortcode->registerShortcode('user_feedback');

// ============================================
// EXAMPLE 2: Form with Custom Data
// ============================================

$jobApplicationForm = new FormBuilder('job_application', 'bootstrap');
$jobApplicationForm->method('POST')->action($_SERVER['PHP_SELF']);
$jobApplicationForm->field((new TextField('name'))->label('Full Name')->required());
$jobApplicationForm->field((new EmailField('email'))->label('Email')->required());
$jobApplicationForm->field((new TextField('phone'))->label('Phone Number')->required());
$jobApplicationForm->field((new TextAreaField('cover_letter'))->label('Cover Letter')->required()->rule('min:50'));

$jobShortcode = new FormShortcode($jobApplicationForm);

// Set custom data (job position, company, etc.)
$jobShortcode->setData([
    'job_id' => 'dev-001',
    'position' => 'Senior PHP Developer',
    'company' => 'Tech Corp',
    'department' => 'Engineering'
]);

$jobShortcode->onSuccess(function ($data, $shortcode) {
    $jobData = $shortcode->getData();
    
    // Create application record
    $applicationId = rand(10000, 99999);
    
    error_log("Job application received for {$jobData['position']} from {$data['name']}");
    
    return [
        'application_id' => $applicationId,
        'job_id' => $jobData['job_id'],
        'position' => $jobData['position']
    ];
});

$jobShortcode->registerShortcode('job_application');

// ============================================
// EXAMPLE 3: Form with Conditional Fields
// ============================================

$surveyForm = new FormBuilder('customer_survey', 'bootstrap');
$surveyForm->method('POST')->action($_SERVER['PHP_SELF']);
$surveyForm->field((new TextField('name'))->label('Name')->required());
$surveyForm->field((new SelectField('product'))
    ->label('Which product did you purchase?')
    ->required()
    ->options(['product_a' => 'Product A', 'product_b' => 'Product B', 'product_c' => 'Product C']));
$surveyForm->field((new SelectField('satisfaction'))
    ->label('How satisfied are you?')
    ->required()
    ->options(['very_satisfied' => 'Very Satisfied', 'satisfied' => 'Satisfied', 'neutral' => 'Neutral', 'dissatisfied' => 'Dissatisfied']));
$surveyForm->field((new TextAreaField('comments'))->label('Additional Comments'));

$surveyShortcode = new FormShortcode($surveyForm);

$surveyShortcode->onSuccess(function ($data, $shortcode) {
    $surveyId = rand(100000, 999999);
    
    // Store survey response
    error_log("Survey response: Product={$data['product']}, Satisfaction={$data['satisfaction']}");
    
    return ['survey_id' => $surveyId];
});

$surveyShortcode->registerShortcode('customer_survey');

// ============================================
// EXAMPLE 4: Form with Validation Feedback
// ============================================

$newsletterForm = new FormBuilder('newsletter_advanced', 'bootstrap');
$newsletterForm->method('POST')->action($_SERVER['PHP_SELF']);
$newsletterForm->field((new TextField('email'))->label('Email Address')->required()->rule('email'));
$newsletterForm->field((new CheckboxField('terms'))->label('I agree to receive emails')->required());

$newsletterShortcode = new FormShortcode($newsletterForm);

$newsletterShortcode->onSuccess(function ($data, $shortcode) {
    error_log("Newsletter subscription: {$data['email']}");
    return ['subscribed' => true];
});

$newsletterShortcode->onFail(function ($errors, $shortcode) {
    // Log validation failures
    error_log("Newsletter subscription failed: " . json_encode($errors));
    return null;
});

$newsletterShortcode->registerShortcode('newsletter_advanced');

// ============================================
// CONTENT WITH MULTIPLE SHORTCODES
// ============================================

$pageContent = <<<HTML
<div class="container">
    <h1>Customer Engagement Hub</h1>
    
    <div class="row">
        <div class="col-md-6">
            <h2>Share Your Feedback</h2>
            <p>Help us improve by sharing your experience:</p>
            [user_feedback style="compact"]
        </div>
        
        <div class="col-md-6">
            <h2>Join Our Newsletter</h2>
            <p>Stay updated with our latest news:</p>
            [newsletter_advanced]
        </div>
    </div>
    
    <hr>
    
    <h2>Customer Survey</h2>
    <p>We'd love to hear about your experience with our products:</p>
    [customer_survey]
    
    <hr>
    
    <h2>Career Opportunities</h2>
    <p>Interested in joining our team? Apply for the Senior PHP Developer position:</p>
    [job_application]
</div>
HTML;

// ============================================
// RENDER OUTPUT
// ============================================

if (php_sapi_name() === 'cli') {
    echo "=== Advanced Shortcode Examples ===\n\n";
    echo "Registered Shortcodes:\n";
    foreach (ShortcodeManager::getAll() as $shortcode) {
        echo "  - [$shortcode]\n";
    }
    echo "\n";
    echo ShortcodeManager::parse($pageContent);
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Advanced Shortcode Examples</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { padding: 20px; background: #f5f5f5; }
            .container { background: white; padding: 30px; border-radius: 8px; }
            h1 { margin-bottom: 30px; color: #333; }
            h2 { margin-top: 30px; margin-bottom: 15px; color: #555; }
            hr { margin: 40px 0; }
            .row { margin-bottom: 30px; }
        </style>
    </head>
    <body>
        <?php echo ShortcodeManager::parse($pageContent); ?>
    </body>
    </html>
    <?php
}

