<?php
/**
 * Run All Tests
 * Comprehensive test suite for Tweekersnut Forms Library
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Tweekersnut Forms Library - Comprehensive Test Suite         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$tests = [
    'test-examples.php' => 'Basic Library Tests',
    'test-form-submission.php' => 'Form Submission & Validation',
    'test-ajax-submission.php' => 'AJAX Form Submission',
    'test-csrf-handling.php' => 'CSRF Token Handling'
];

$testResults = [];

foreach ($tests as $file => $description) {
    echo "\n";
    echo "┌────────────────────────────────────────────────────────────────┐\n";
    echo "│ Running: $description\n";
    echo "│ File: $file\n";
    echo "└────────────────────────────────────────────────────────────────┘\n";
    echo "\n";
    
    if (file_exists($file)) {
        ob_start();
        try {
            include $file;
            $output = ob_get_clean();
            echo $output;
            $testResults[$file] = 'PASSED';
            echo "\n✓ Test completed successfully\n";
        } catch (Exception $e) {
            ob_end_clean();
            echo "✗ Test failed with error: " . $e->getMessage() . "\n";
            $testResults[$file] = 'FAILED';
        }
    } else {
        echo "✗ Test file not found: $file\n";
        $testResults[$file] = 'NOT FOUND';
    }
}

// Summary
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$passed = 0;
$failed = 0;
$notFound = 0;

foreach ($testResults as $file => $result) {
    $status = match($result) {
        'PASSED' => '✓',
        'FAILED' => '✗',
        'NOT FOUND' => '?'
    };
    
    echo "$status $file: $result\n";
    
    if ($result === 'PASSED') $passed++;
    elseif ($result === 'FAILED') $failed++;
    else $notFound++;
}

echo "\n";
echo "Total Tests: " . count($testResults) . "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Not Found: $notFound\n";
echo "\n";

if ($failed === 0 && $notFound === 0) {
    echo "✓ All tests passed!\n";
} else {
    echo "✗ Some tests failed or were not found.\n";
}

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "\n";

