<?php
/**
 * Test Form Submission and Validation
 * Demonstrates what happens when forms are submitted with valid/invalid data
 */

require_once __DIR__ . '/vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\TextAreaField;
use Tweekersnut\FormsLib\Fields\SubmitField;

echo "========================================\n";
echo "Form Submission & Validation Test\n";
echo "========================================\n\n";

// Create form
$form = new FormBuilder('contact_form', 'bootstrap');
$form->method('POST')->action($_SERVER['PHP_SELF']);

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
    (new TextField('phone'))
        ->label('Phone Number')
        ->placeholder('+1 (555) 123-4567')
        ->rule('phone')
);

$form->field(
    (new TextAreaField('message'))
        ->label('Message')
        ->placeholder('Your message here...')
        ->required()
        ->rule('min:10')
        ->rule('max:1000')
);

$form->field(new SubmitField('submit', 'Send Message'));

// Test Case 1: Valid Data
echo "TEST CASE 1: Valid Form Data\n";
echo "----------------------------\n";
$validData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1 (555) 123-4567',
    'message' => 'This is a test message with enough content to pass validation rules.',
    'submit' => 'Send Message'
];

echo "Submitted Data:\n";
echo json_encode($validData, JSON_PRETTY_PRINT) . "\n\n";

$isValid = $form->validate($validData);
echo "Validation Result: " . ($isValid ? "✓ PASSED" : "✗ FAILED") . "\n";
echo "Errors: " . count($form->getErrors()) . "\n";

if ($isValid) {
    echo "\n✓ Form is valid! Processing data...\n";
    echo "Processed Data:\n";
    echo json_encode($validData, JSON_PRETTY_PRINT) . "\n";
}
echo "\n";

// Test Case 2: Invalid Data - Missing Required Fields
echo "TEST CASE 2: Missing Required Fields\n";
echo "------------------------------------\n";
$form2 = new FormBuilder('contact_form', 'bootstrap');
$form2->field((new TextField('name'))->label('Name')->required());
$form2->field((new EmailField('email'))->label('Email')->required());
$form2->field((new TextAreaField('message'))->label('Message')->required());

$invalidData1 = [
    'name' => '',
    'email' => '',
    'message' => ''
];

echo "Submitted Data:\n";
echo json_encode($invalidData1, JSON_PRETTY_PRINT) . "\n\n";

$isValid = $form2->validate($invalidData1);
echo "Validation Result: " . ($isValid ? "✓ PASSED" : "✗ FAILED") . "\n";
echo "Errors Found: " . count($form2->getErrors()) . "\n";
echo "Error Details:\n";
foreach ($form2->getErrors() as $field => $errors) {
    echo "  - $field: " . implode(", ", $errors) . "\n";
}
echo "\n";

// Test Case 3: Invalid Data - Wrong Format
echo "TEST CASE 3: Invalid Email Format\n";
echo "--------------------------------\n";
$form3 = new FormBuilder('contact_form', 'bootstrap');
$form3->field((new EmailField('email'))->label('Email')->required());

$invalidData2 = [
    'email' => 'not-an-email'
];

echo "Submitted Data:\n";
echo json_encode($invalidData2, JSON_PRETTY_PRINT) . "\n\n";

$isValid = $form3->validate($invalidData2);
echo "Validation Result: " . ($isValid ? "✓ PASSED" : "✗ FAILED") . "\n";
echo "Errors Found: " . count($form3->getErrors()) . "\n";
echo "Error Details:\n";
foreach ($form3->getErrors() as $field => $errors) {
    echo "  - $field: " . implode(", ", $errors) . "\n";
}
echo "\n";

// Test Case 4: Invalid Data - Min/Max Length
echo "TEST CASE 4: Min/Max Length Validation\n";
echo "-------------------------------------\n";
$form4 = new FormBuilder('contact_form', 'bootstrap');
$form4->field(
    (new TextField('username'))
        ->label('Username')
        ->required()
        ->rule('min:3')
        ->rule('max:20')
);

$invalidData3 = [
    'username' => 'ab'  // Too short
];

echo "Submitted Data:\n";
echo json_encode($invalidData3, JSON_PRETTY_PRINT) . "\n\n";

$isValid = $form4->validate($invalidData3);
echo "Validation Result: " . ($isValid ? "✓ PASSED" : "✗ FAILED") . "\n";
echo "Errors Found: " . count($form4->getErrors()) . "\n";
echo "Error Details:\n";
foreach ($form4->getErrors() as $field => $errors) {
    echo "  - $field: " . implode(", ", $errors) . "\n";
}
echo "\n";

// Test Case 5: Form with Values Set
echo "TEST CASE 5: Form with Pre-filled Values\n";
echo "--------------------------------------\n";
$form5 = new FormBuilder('contact_form', 'bootstrap');
$form5->field((new TextField('name'))->label('Name')->required());
$form5->field((new EmailField('email'))->label('Email')->required());

$form5->values([
    'name' => 'Jane Smith',
    'email' => 'jane@example.com'
]);

echo "Form HTML with pre-filled values:\n";
echo $form5->render();
echo "\n";

echo "========================================\n";
echo "All test cases completed!\n";
echo "========================================\n";

