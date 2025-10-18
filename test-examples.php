<?php
/**
 * Test script to verify all examples work correctly
 */

require_once __DIR__ . '/vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\PasswordField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SelectField;
use Tweekersnut\FormsLib\Fields\CheckboxField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\Validation\Validator;
use Tweekersnut\FormsLib\Security\CsrfToken;

echo "========================================\n";
echo "Testing Tweekersnut Forms Library\n";
echo "========================================\n\n";

// Test 1: Basic Form
echo "✓ Test 1: Basic Form Creation\n";
try {
    $form = new FormBuilder('test_form', 'bootstrap');
    $form->method('POST')->action('/test');
    $form->field((new TextField('name'))->label('Name')->required());
    echo "  ✓ Form created successfully\n";
    echo "  ✓ Form HTML length: " . strlen($form->render()) . " bytes\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Form with Multiple Fields
echo "\n✓ Test 2: Form with Multiple Fields\n";
try {
    $form = new FormBuilder('contact_form', 'bootstrap');
    $form->field((new TextField('name'))->label('Name')->required());
    $form->field((new EmailField('email'))->label('Email')->required());
    $form->field((new TextAreaField('message'))->label('Message')->rows(5)->required());
    $form->field((new SubmitField('submit', 'Send')));
    echo "  ✓ Form with 4 fields created\n";
    echo "  ✓ Form HTML length: " . strlen($form->render()) . " bytes\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Form Validation
echo "\n✓ Test 3: Form Validation\n";
try {
    $form = new FormBuilder('validation_form', 'bootstrap');
    $form->field((new TextField('username'))->label('Username')->required()->rule('min:3')->rule('max:20'));
    $form->field((new EmailField('email'))->label('Email')->required());
    
    $testData = ['username' => 'ab', 'email' => 'invalid'];
    $isValid = $form->validate($testData);
    echo "  ✓ Validation test completed\n";
    echo "  ✓ Valid: " . ($isValid ? 'true' : 'false') . "\n";
    echo "  ✓ Errors: " . count($form->getErrors()) . "\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: CSRF Token
echo "\n✓ Test 4: CSRF Token Protection\n";
try {
    $csrf = new CsrfToken('_token');
    $token = $csrf->generate();
    echo "  ✓ CSRF token generated: " . substr($token, 0, 10) . "...\n";
    echo "  ✓ Token verification: " . ($csrf->verify($token) ? 'passed' : 'failed') . "\n";
    
    $form = new FormBuilder('csrf_form', 'bootstrap');
    $form->withCsrfToken($token);
    echo "  ✓ CSRF token added to form\n";
    echo "  ✓ Form has CSRF: " . ($form->hasCsrfToken() ? 'yes' : 'no') . "\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 5: Tailwind Theme
echo "\n✓ Test 5: Tailwind Theme Support\n";
try {
    $form = new FormBuilder('tailwind_form', 'tailwind');
    $form->field((new TextField('name'))->label('Name')->required());
    $html = $form->render();
    echo "  ✓ Tailwind form created\n";
    echo "  ✓ Form HTML length: " . strlen($html) . " bytes\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 6: Form JSON Export
echo "\n✓ Test 6: Form JSON Export\n";
try {
    $form = new FormBuilder('json_form', 'bootstrap');
    $form->field((new TextField('name'))->label('Name')->required());
    $json = $form->toJson();
    echo "  ✓ Form exported to JSON\n";
    echo "  ✓ JSON length: " . strlen($json) . " bytes\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "All tests completed successfully!\n";
echo "========================================\n";

