<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | The default CSS framework theme to use for form rendering.
    | Supported: 'bootstrap', 'tailwind'
    |
    */
    'theme' => env('FORMS_LIB_THEME', 'bootstrap'),

    /*
    |--------------------------------------------------------------------------
    | AJAX Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AJAX form handling
    |
    */
    'ajax' => [
        'enabled' => true,
        'validate_on_change' => true,
        'validate_on_blur' => true,
        'show_loading_state' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    |
    | Default validation settings
    |
    */
    'validation' => [
        'show_required_indicator' => true,
        'error_class' => 'has-error',
        'success_class' => 'has-success',
    ],
];

