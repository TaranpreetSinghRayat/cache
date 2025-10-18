<?php

namespace Tweekersnut\FormsLib\Laravel;

use Illuminate\Support\ServiceProvider;
use Tweekersnut\FormsLib\Shortcodes\ShortcodeManager;

class ShortcodeServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->app->singleton('shortcodes', function () {
            return new ShortcodeManager();
        });
    }

    /**
     * Boot services
     */
    public function boot(): void
    {
        // Register Blade directive for shortcodes
        $this->registerBladeDirectives();

        // Load shortcodes from config
        $this->loadShortcodesFromConfig();
    }

    /**
     * Register Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // @shortcode('contact_form')
        \Blade::directive('shortcode', function ($expression) {
            return "<?php echo \\Tweekersnut\\FormsLib\\Shortcodes\\ShortcodeManager::execute({$expression}); ?>";
        });

        // @shortcodes($content)
        \Blade::directive('shortcodes', function ($expression) {
            return "<?php echo \\Tweekersnut\\FormsLib\\Shortcodes\\ShortcodeManager::parse({$expression}); ?>";
        });
    }

    /**
     * Load shortcodes from config
     */
    protected function loadShortcodesFromConfig(): void
    {
        $config = config('forms-lib.shortcodes', []);

        foreach ($config as $name => $callback) {
            if (is_callable($callback)) {
                ShortcodeManager::register($name, $callback);
            }
        }
    }
}

