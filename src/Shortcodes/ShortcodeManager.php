<?php

namespace Tweekersnut\FormsLib\Shortcodes;

use Tweekersnut\FormsLib\Core\FormBuilder;
use Closure;

class ShortcodeManager
{
    /**
     * Registered shortcodes
     */
    protected static array $shortcodes = [];

    /**
     * Register a shortcode
     */
    public static function register(string $name, Closure $callback): void
    {
        self::$shortcodes[$name] = $callback;
    }

    /**
     * Check if shortcode exists
     */
    public static function exists(string $name): bool
    {
        return isset(self::$shortcodes[$name]);
    }

    /**
     * Execute shortcode
     */
    public static function execute(string $name, array $attributes = []): string
    {
        if (!self::exists($name)) {
            return "[Shortcode '{$name}' not found]";
        }

        try {
            $callback = self::$shortcodes[$name];
            return $callback($attributes);
        } catch (\Exception $e) {
            return "[Shortcode '{$name}' error: " . $e->getMessage() . "]";
        }
    }

    /**
     * Parse and execute shortcodes in content
     */
    public static function parse(string $content): string
    {
        // Pattern: [shortcode_name param1="value1" param2="value2"]
        $pattern = '/\[([a-zA-Z_][a-zA-Z0-9_]*)((?:\s+[a-zA-Z_][a-zA-Z0-9_]*="[^"]*")*)\]/';

        return preg_replace_callback($pattern, function ($matches) {
            $name = $matches[1];
            $attrString = $matches[2];

            // Parse attributes
            $attributes = [];
            if (!empty($attrString)) {
                $attrPattern = '/\s+([a-zA-Z_][a-zA-Z0-9_]*)="([^"]*)"/';
                preg_match_all($attrPattern, $attrString, $attrMatches);

                for ($i = 0; $i < count($attrMatches[1]); $i++) {
                    $attributes[$attrMatches[1][$i]] = $attrMatches[2][$i];
                }
            }

            return self::execute($name, $attributes);
        }, $content);
    }

    /**
     * Get all registered shortcodes
     */
    public static function getAll(): array
    {
        return array_keys(self::$shortcodes);
    }

    /**
     * Unregister a shortcode
     */
    public static function unregister(string $name): void
    {
        unset(self::$shortcodes[$name]);
    }

    /**
     * Clear all shortcodes
     */
    public static function clear(): void
    {
        self::$shortcodes = [];
    }
}

