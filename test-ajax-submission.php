<?php
/**
 * Test AJAX Form Submission
 * Demonstrates AJAX request handling and JSON responses
 */

require_once __DIR__ . '/vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\SelectField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\AJAX\AjaxHandler;

echo "========================================\n";
echo "AJAX Form Submission Test\n";
echo "========================================\n\n";

// Create form
$form = new FormBuilder('subscription_form', 'bootstrap');
$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field((new SelectField('plan'))->label('Plan')->required());
$form->field(new SubmitField('submit', 'Subscribe'));

// Test Case 1: Valid AJAX Request
echo "TEST CASE 1: Valid AJAX Request\n";
echo "-------------------------------\n";

$validAjaxData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'plan' => 'pro',
    'submit' => 'Subscribe'
];

echo "AJAX Request Data (JSON):\n";
echo json_encode($validAjaxData, JSON_PRETTY_PRINT) . "\n\n";

// Simulate AJAX validation
$isValid = $form->validate($validAjaxData);

$response = [
    'success' => $isValid,
    'message' => $isValid ? 'Form submitted successfully!' : 'Validation failed',
    'data' => $isValid ? $validAjaxData : null,
    'errors' => $form->getErrors()
];

echo "AJAX Response (JSON):\n";
echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 2: Invalid AJAX Request - Missing Fields
echo "TEST CASE 2: Invalid AJAX Request - Missing Fields\n";
echo "------------------------------------------------\n";

$invalidAjaxData = [
    'name' => '',
    'email' => 'invalid-email',
    'plan' => ''
];

echo "AJAX Request Data (JSON):\n";
echo json_encode($invalidAjaxData, JSON_PRETTY_PRINT) . "\n\n";

$form2 = new FormBuilder('subscription_form', 'bootstrap');
$form2->field((new TextField('name'))->label('Name')->required());
$form2->field((new EmailField('email'))->label('Email')->required());
$form2->field((new SelectField('plan'))->label('Plan')->required());

$isValid = $form2->validate($invalidAjaxData);

$response = [
    'success' => $isValid,
    'message' => $isValid ? 'Form submitted successfully!' : 'Validation failed',
    'data' => $isValid ? $invalidAjaxData : null,
    'errors' => $form2->getErrors(),
    'errorCount' => count($form2->getErrors())
];

echo "AJAX Response (JSON):\n";
echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 3: AJAX Handler Response
echo "TEST CASE 3: AJAX Handler Response Format\n";
echo "--------------------------------------\n";

$form3 = new FormBuilder('contact_form', 'bootstrap');
$form3->field((new TextField('name'))->label('Name')->required());
$form3->field((new EmailField('email'))->label('Email')->required());

$testData = [
    'name' => 'Jane Smith',
    'email' => 'jane@example.com'
];

echo "Form Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

$isValid = $form3->validate($testData);

// Simulate AjaxHandler response
$ajaxResponse = [
    'status' => $isValid ? 'success' : 'error',
    'message' => $isValid ? 'Form processed successfully' : 'Form validation failed',
    'data' => [
        'formName' => $form3->getName(),
        'method' => $form3->getMethod(),
        'action' => $form3->getAction(),
        'theme' => 'bootstrap',
        'fieldCount' => count($form3->getFields()),
        'isValid' => $isValid,
        'submittedData' => $testData,
        'errors' => $form3->getErrors()
    ]
];

echo "AJAX Handler Response (JSON):\n";
echo json_encode($ajaxResponse, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 4: Error Response with Details
echo "TEST CASE 4: Detailed Error Response\n";
echo "-----------------------------------\n";

$form4 = new FormBuilder('registration_form', 'bootstrap');
$form4->field(
    (new TextField('username'))
        ->label('Username')
        ->required()
        ->rule('min:3')
        ->rule('max:20')
);
$form4->field((new EmailField('email'))->label('Email')->required());
$form4->field(
    (new TextField('password'))
        ->label('Password')
        ->required()
        ->rule('min:8')
);

$invalidData = [
    'username' => 'ab',  // Too short
    'email' => 'not-email',  // Invalid format
    'password' => '123'  // Too short
];

echo "Form Data:\n";
echo json_encode($invalidData, JSON_PRETTY_PRINT) . "\n\n";

$isValid = $form4->validate($invalidData);

$errorResponse = [
    'status' => 'error',
    'message' => 'Form validation failed with ' . count($form4->getErrors()) . ' error(s)',
    'errors' => $form4->getErrors(),
    'data' => [
        'formName' => $form4->getName(),
        'submittedData' => $invalidData,
        'validationStatus' => $isValid ? 'valid' : 'invalid'
    ]
];

echo "Error Response (JSON):\n";
echo json_encode($errorResponse, JSON_PRETTY_PRINT) . "\n\n";

echo "========================================\n";
echo "All AJAX test cases completed!\n";
echo "========================================\n";

