<?php
/**
 * Test CSRF Token Handling
 * Demonstrates CSRF token generation, verification, and form integration
 */

require_once __DIR__ . '/vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;
use Tweekersnut\FormsLib\Fields\SubmitField;
use Tweekersnut\FormsLib\Security\CsrfToken;

echo "========================================\n";
echo "CSRF Token Handling Test\n";
echo "========================================\n\n";

// Test Case 1: Token Generation
echo "TEST CASE 1: CSRF Token Generation\n";
echo "--------------------------------\n";

$csrf = new CsrfToken('_token');
$token = $csrf->generate();

echo "Token Name: _token\n";
echo "Generated Token: " . substr($token, 0, 20) . "...\n";
echo "Token Length: " . strlen($token) . " characters\n";
echo "Token Type: " . gettype($token) . "\n\n";

// Test Case 2: Token Verification
echo "TEST CASE 2: Token Verification\n";
echo "-----------------------------\n";

$csrf2 = new CsrfToken('_token');
$token = $csrf2->generate();

echo "Generated Token: " . substr($token, 0, 20) . "...\n";
echo "Verifying Token...\n";

$isValid = $csrf2->verify($token);
echo "Verification Result: " . ($isValid ? "✓ VALID" : "✗ INVALID") . "\n\n";

// Test Case 3: Invalid Token Verification
echo "TEST CASE 3: Invalid Token Verification\n";
echo "------------------------------------\n";

$csrf3 = new CsrfToken('_token');
$validToken = $csrf3->generate();
$invalidToken = 'invalid_token_' . bin2hex(random_bytes(32));

echo "Valid Token: " . substr($validToken, 0, 20) . "...\n";
echo "Invalid Token: " . substr($invalidToken, 0, 20) . "...\n";

$isValid = $csrf3->verify($invalidToken);
echo "Verification Result: " . ($isValid ? "✓ VALID" : "✗ INVALID") . "\n\n";

// Test Case 4: Form with CSRF Token
echo "TEST CASE 4: Form with CSRF Token\n";
echo "-------------------------------\n";

$csrf4 = new CsrfToken('_token');
$token = $csrf4->generate();

$form = new FormBuilder('secure_form', 'bootstrap');
$form->method('POST')
    ->action('/submit')
    ->withCsrfToken($token);

$form->field((new TextField('name'))->label('Name')->required());
$form->field((new EmailField('email'))->label('Email')->required());
$form->field(new SubmitField('submit', 'Submit'));

echo "Form Name: " . $form->getName() . "\n";
echo "Has CSRF Token: " . ($form->hasCsrfToken() ? "Yes" : "No") . "\n";
echo "CSRF Token Name: " . $form->getCsrfTokenName() . "\n";
echo "CSRF Token Value: " . substr($form->getCsrfTokenValue(), 0, 20) . "...\n\n";

echo "Form HTML (with CSRF token):\n";
echo $form->render();
echo "\n\n";

// Test Case 5: CSRF Token in Form Submission
echo "TEST CASE 5: CSRF Token Verification in Submission\n";
echo "----------------------------------------------\n";

$csrf5 = new CsrfToken('_token');
$token = $csrf5->generate();

$submittedData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    '_token' => $token,
    'submit' => 'Submit'
];

echo "Submitted Data:\n";
echo json_encode($submittedData, JSON_PRETTY_PRINT) . "\n\n";

$tokenFromSubmission = $submittedData['_token'];
$isTokenValid = $csrf5->verify($tokenFromSubmission);

$response = [
    'tokenValid' => $isTokenValid,
    'message' => $isTokenValid ? 'CSRF token verified successfully' : 'CSRF token verification failed',
    'submittedToken' => substr($tokenFromSubmission, 0, 20) . '...',
    'tokenLength' => strlen($tokenFromSubmission)
];

echo "Verification Response:\n";
echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 6: Token Regeneration
echo "TEST CASE 6: Token Regeneration\n";
echo "-----------------------------\n";

$csrf6 = new CsrfToken('_token');
$oldToken = $csrf6->generate();
echo "Old Token: " . substr($oldToken, 0, 20) . "...\n";

$newToken = $csrf6->regenerate();
echo "New Token: " . substr($newToken, 0, 20) . "...\n";

echo "Old Token Still Valid: " . ($csrf6->verify($oldToken) ? "Yes" : "No") . "\n";
echo "New Token Valid: " . ($csrf6->verify($newToken) ? "Yes" : "No") . "\n\n";

// Test Case 7: Multiple Forms with Different Tokens
echo "TEST CASE 7: Multiple Forms with Different Tokens\n";
echo "----------------------------------------------\n";

$csrf7a = new CsrfToken('_token');
$csrf7b = new CsrfToken('_token');

$token1 = $csrf7a->generate();
$token2 = $csrf7b->generate();

echo "Form 1 Token: " . substr($token1, 0, 20) . "...\n";
echo "Form 2 Token: " . substr($token2, 0, 20) . "...\n";
echo "Tokens are different: " . ($token1 !== $token2 ? "Yes" : "No") . "\n\n";

$form1 = new FormBuilder('form1', 'bootstrap');
$form1->withCsrfToken($token1);

$form2 = new FormBuilder('form2', 'bootstrap');
$form2->withCsrfToken($token2);

echo "Form 1 CSRF Token: " . substr($form1->getCsrfTokenValue(), 0, 20) . "...\n";
echo "Form 2 CSRF Token: " . substr($form2->getCsrfTokenValue(), 0, 20) . "...\n\n";

// Test Case 8: Security Response
echo "TEST CASE 8: Security Response Format\n";
echo "-----------------------------------\n";

$csrf8 = new CsrfToken('_token');
$token = $csrf8->generate();

$securityResponse = [
    'csrfProtection' => [
        'enabled' => true,
        'tokenName' => '_token',
        'tokenLength' => strlen($token),
        'tokenAlgorithm' => 'SHA256',
        'tokenEncoding' => 'hex',
        'verificationMethod' => 'hash_equals (timing attack safe)'
    ],
    'formIntegration' => [
        'automaticInjection' => true,
        'hiddenFieldType' => 'hidden',
        'fieldName' => '_token'
    ],
    'recommendations' => [
        'Always regenerate token after successful form submission',
        'Use HTTPS to prevent token interception',
        'Store token in session for server-side verification',
        'Validate token on every POST/PUT/DELETE request'
    ]
];

echo "Security Configuration:\n";
echo json_encode($securityResponse, JSON_PRETTY_PRINT) . "\n\n";

echo "========================================\n";
echo "All CSRF token test cases completed!\n";
echo "========================================\n";

