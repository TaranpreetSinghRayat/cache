<?php

namespace Tweekersnut\FormsLib\Laravel;

use Illuminate\Support\ServiceProvider;
use Tweekersnut\FormsLib\Core\FormBuilder;

class FormsLibServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->app->singleton('forms-lib', function ($app) {
            return new FormsLibManager($app);
        });
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/config/forms-lib.php' => config_path('forms-lib.php'),
        ], 'forms-lib-config');

        // Publish assets
        $this->publishes([
            __DIR__ . '/../AJAX/form-handler.js' => public_path('js/form-handler.js'),
        ], 'forms-lib-assets');

        // Register helper functions
        $this->registerHelpers();
    }

    /**
     * Register helper functions
     */
    protected function registerHelpers(): void
    {
        if (!function_exists('form')) {
            function form(string $name, string $theme = 'bootstrap'): FormBuilder
            {
                return app('forms-lib')->create($name, $theme);
            }
        }

        if (!function_exists('form_field')) {
            function form_field(FormBuilder $form, string $fieldName): string
            {
                return $form->renderField($fieldName);
            }
        }

        if (!function_exists('form_render')) {
            function form_render(FormBuilder $form): string
            {
                return $form->render();
            }
        }
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return ['forms-lib'];
    }
}

