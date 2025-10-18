<?php

require_once __DIR__ . '/vendor/autoload.php';

use Tweekersnut\FormsLib\Core\FormBuilder;
use Tweekersnut\FormsLib\Fields\TextField;
use Tweekersnut\FormsLib\Fields\EmailField;

try {
    $form = new FormBuilder('test_form', 'bootstrap');
    $form->method('POST')->action('/test');
    
    $form->field(
        (new TextField('name'))
            ->label('Name')
            ->required()
    );
    
    $form->field(
        (new EmailField('email'))
            ->label('Email')
            ->required()
    );
    
    echo "Form created successfully!\n";
    echo "Form HTML:\n";
    echo $form->render();
    echo "\n\nForm JSON:\n";
    echo $form->toJson();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

