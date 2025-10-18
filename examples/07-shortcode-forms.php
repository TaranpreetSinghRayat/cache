<?php
/**
 * Shortcode Forms Example
 * 
 * This example demonstrates how to use the Shortcode system
 * to create reusable forms that can be embedded in forum posts,
 * pages, or any content using simple shortcode syntax.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SelectField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\Shortcodes\ShortcodeManager;
use Tweekersnut\FormsLib\Shortcodes\FormShortcode;

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// EXAMPLE 1: Simple Contact Form Shortcode
// ============================================

$contactForm = new FormBuilder('contact_form', 'bootstrap');
$contactForm->method('POST')
    ->action($_SERVER['PHP_SELF'])
    ->field((new TextField('name'))->label('Your Name')->required())
    ->field((new EmailField('email'))->label('Email Address')->required())
    ->field((new TextAreaField('message'))->label('Message')->required()->rule('min:10'));

$contactShortcode = new FormShortcode($contactForm);

// Success callback
$contactShortcode->onSuccess(function ($data, $shortcode) {
    // Here you would typically:
    // - Save to database
    // - Send email
    // - Log the submission
    
    echo "<!-- Contact form submitted: " . htmlspecialchars($data['name']) . " -->\n";
    
    // Return custom data or null
    return null;
});

// Failure callback
$contactShortcode->onFail(function ($errors, $shortcode) {
    // Handle validation failures
    echo "<!-- Contact form validation failed -->\n";
    return null;
});

// Register shortcode
$contactShortcode->registerShortcode('contact_form');

// ============================================
// EXAMPLE 2: Support Ticket Form Shortcode
// ============================================

$ticketForm = new FormBuilder('support_ticket', 'bootstrap');
$ticketForm->method('POST')
    ->action($_SERVER['PHP_SELF'])
    ->field((new TextField('name'))->label('Your Name')->required())
    ->field((new EmailField('email'))->label('Email Address')->required())
    ->field((new SelectField('priority'))
        ->label('Priority')
        ->required()
        ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']))
    ->field((new TextAreaField('description'))->label('Issue Description')->required()->rule('min:20'));

$ticketShortcode = new FormShortcode($ticketForm);

$ticketShortcode->onSuccess(function ($data, $shortcode) {
    // Create ticket in database
    $ticketId = rand(1000, 9999);
    
    return [
        'ticket_id' => $ticketId,
        'status' => 'created'
    ];
});

$ticketShortcode->beforeRender(function ($form, $attributes) {
    // Modify form before rendering
    if (isset($attributes['user_id'])) {
        // Could pre-fill user info
    }
});

$ticketShortcode->registerShortcode('support_ticket');

// ============================================
// EXAMPLE 3: Newsletter Signup Shortcode
// ============================================

$newsletterForm = new FormBuilder('newsletter_signup', 'bootstrap');
$newsletterForm->method('POST')
    ->action($_SERVER['PHP_SELF'])
    ->field((new TextField('name'))->label('Full Name')->required())
    ->field((new EmailField('email'))->label('Email Address')->required())
    ->field((new SelectField('frequency'))
        ->label('Email Frequency')
        ->options(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly']));

$newsletterShortcode = new FormShortcode($newsletterForm);

$newsletterShortcode->onSuccess(function ($data, $shortcode) {
    // Subscribe to newsletter
    // Save to database
    return ['subscribed' => true];
});

$newsletterShortcode->registerShortcode('newsletter_signup');

// ============================================
// EXAMPLE 4: Forum Post Form Shortcode
// ============================================

$postForm = new FormBuilder('forum_post', 'bootstrap');
$postForm->method('POST')
    ->action($_SERVER['PHP_SELF'])
    ->field((new TextField('title'))->label('Post Title')->required()->rule('min:5')->rule('max:200'))
    ->field((new TextAreaField('content'))->label('Post Content')->required()->rule('min:20'));

$postShortcode = new FormShortcode($postForm);

$postShortcode->onSuccess(function ($data, $shortcode) {
    // Create forum post
    $postId = rand(10000, 99999);
    return ['post_id' => $postId, 'created_at' => date('Y-m-d H:i:s')];
});

$postShortcode->registerShortcode('forum_post');

// ============================================
// USAGE IN CONTENT
// ============================================

// Simulate forum/page content with shortcodes
$forumContent = <<<HTML
<h1>Welcome to Our Forum</h1>

<h2>Contact Us</h2>
<p>Use the form below to get in touch:</p>
[contact_form]

<hr>

<h2>Report an Issue</h2>
<p>Having problems? Submit a support ticket:</p>
[support_ticket priority="high"]

<hr>

<h2>Subscribe to Newsletter</h2>
<p>Stay updated with our latest news:</p>
[newsletter_signup]

<hr>

<h2>Create Forum Post</h2>
<p>Share your thoughts:</p>
[forum_post]
HTML;

// ============================================
// RENDER OUTPUT
// ============================================

if (php_sapi_name() === 'cli') {
    // CLI mode - show registered shortcodes
    echo "=== Registered Shortcodes ===\n";
    foreach (ShortcodeManager::getAll() as $shortcode) {
        echo "- [$shortcode]\n";
    }
    echo "\n";
    
    // Parse and render content
    echo "=== Forum Content ===\n";
    echo ShortcodeManager::parse($forumContent);
} else {
    // Web mode
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Shortcode Forms Example</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { padding: 20px; }
            hr { margin: 40px 0; }
            h2 { margin-top: 30px; color: #333; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <?php echo ShortcodeManager::parse($forumContent); ?>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Registered Shortcodes</h5>
                        </div>
                        <div class="card-body">
                            <ul>
                                <?php foreach (ShortcodeManager::getAll() as $shortcode): ?>
                                    <li><code>[<?php echo $shortcode; ?>]</code></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}

