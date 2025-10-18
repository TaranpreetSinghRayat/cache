<?php
/**
 * Laravel Integration Example
 * 
 * This example demonstrates how to use Tweekersnut Forms Library
 * within a Laravel application.
 * 
 * INSTALLATION IN LARAVEL:
 * 1. composer require tweekersnut/forms-lib
 * 2. Add service provider to config/app.php:
 *    'providers' => [
 *        ...
 *        Tweekersnut\FormsLib\Laravel\FormsLibServiceProvider::class,
 *    ]
 * 3. Publish config: php artisan vendor:publish --provider="Tweekersnut\FormsLib\Laravel\FormsLibServiceProvider"
 */

/**
 * EXAMPLE 1: Using in a Laravel Controller
 * 
 * File: app/Http/Controllers/UserController.php
 */

namespace App\Http\Controllers;

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\SelectField;
use Tweekersnut\FormsLib\Fields\SubmitField;

class UserController extends Controller
{
    /**
     * Show user registration form
     */
    public function showRegistrationForm()
    {
        // Create form using the library
        $form = new FormBuilder('user_registration', 'bootstrap');
        $form->method('POST')
            ->action(route('user.register'))
            ->withCsrfToken(csrf_token());

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
                ->label('Email Address')
                ->placeholder('john@example.com')
                ->required()
        );

        $form->field(
            (new TextField('password'))
                ->label('Password')
                ->placeholder('Enter a strong password')
                ->required()
                ->rule('min:8')
        );

        $form->field(new SubmitField('submit', 'Register'));

        return view('auth.register', ['form' => $form]);
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        // Create form for validation
        $form = new FormBuilder('user_registration', 'bootstrap');
        $form->field((new TextField('name'))->label('Name')->required());
        $form->field((new EmailField('email'))->label('Email')->required());
        $form->field((new TextField('password'))->label('Password')->required()->rule('min:8'));

        // Validate using the form
        if (!$form->validate($request->all())) {
            return back()
                ->withErrors($form->getErrors())
                ->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }
}

/**
 * EXAMPLE 2: Using in a Blade Template
 * 
 * File: resources/views/auth/register.blade.php
 */

?>
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
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
            </div>
        </div>
    </div>
</div>
@endsection

<?php

/**
 * EXAMPLE 3: Using Helper Functions in Laravel
 * 
 * The service provider registers helper functions for easy access
 */

// Create a form using helper
$form = form('contact_form', 'bootstrap');

// Add fields
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());

// Render in view
echo $form->render();

/**
 * EXAMPLE 4: Using Facade in Laravel
 * 
 * File: app/Http/Controllers/ContactController.php
 */

use Tweekersnut\FormsLib\Laravel\Facades\FormsFacade as Forms;

class ContactController extends Controller
{
    public function showForm()
    {
        // Using facade
        $form = Forms::create('contact_form', 'bootstrap');
        $form->method('POST')->action(route('contact.submit'));
        
        $form->field((new TextField('name'))->label('Name')->required());
        $form->field((new EmailField('email'))->label('Email')->required());
        $form->field((new TextAreaField('message'))->label('Message')->required());
        $form->field(new SubmitField('submit', 'Send'));

        return view('contact', ['form' => $form]);
    }

    public function submit(Request $request)
    {
        $form = Forms::create('contact_form', 'bootstrap');
        $form->field((new TextField('name'))->label('Name')->required());
        $form->field((new EmailField('email'))->label('Email')->required());
        $form->field((new TextAreaField('message'))->label('Message')->required());

        if (!$form->validate($request->all())) {
            return back()->withErrors($form->getErrors())->withInput();
        }

        // Process contact form
        Mail::send('emails.contact', $request->all(), function ($message) {
            $message->to('admin@example.com')
                    ->subject('New Contact Form Submission');
        });

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}

/**
 * EXAMPLE 5: Configuration
 * 
 * File: config/forms-lib.php (published via vendor:publish)
 */

return [
    // Default theme: 'bootstrap' or 'tailwind'
    'theme' => env('FORMS_THEME', 'bootstrap'),

    // AJAX settings
    'ajax' => [
        'enabled' => true,
        'timeout' => 5000,
    ],

    // Validation settings
    'validation' => [
        'show_errors' => true,
        'error_class' => 'is-invalid',
    ],
];

/**
 * EXAMPLE 6: Advanced Usage with Middleware
 * 
 * File: app/Http/Middleware/InjectFormToken.php
 */

namespace App\Http\Middleware;

use Closure;

class InjectFormToken
{
    public function handle($request, Closure $next)
    {
        // Inject CSRF token into all forms
        view()->share('csrf_token', csrf_token());
        
        return $next($request);
    }
}

/**
 * SUMMARY OF LARAVEL INTEGRATION:
 * 
 * 1. Installation:
 *    - composer require tweekersnut/forms-lib
 *    - Register service provider
 *    - Publish config
 * 
 * 2. Usage in Controllers:
 *    - Create FormBuilder instances
 *    - Add fields with validation rules
 *    - Pass to views
 * 
 * 3. Usage in Views:
 *    - Render forms with {!! $form->render() !!}
 *    - Display errors with @if ($errors->any())
 * 
 * 4. Features:
 *    - Automatic CSRF token injection
 *    - Laravel validation integration
 *    - Bootstrap/Tailwind support
 *    - AJAX form handling
 *    - Helper functions and facades
 * 
 * 5. Best Practices:
 *    - Use form validation in controllers
 *    - Always verify CSRF tokens
 *    - Sanitize user input
 *    - Use prepared statements for database
 *    - Implement proper error handling
 */
?>

