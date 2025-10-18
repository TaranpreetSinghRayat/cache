<?php

namespace Tweekersnut\FormsLib\Laravel;

use Tweekersnut\FormsLib\Core\FormBuilder;
use Illuminate\Container\Container;

class FormsLibManager
{
    protected Container $app;
    protected string $defaultTheme = 'bootstrap';

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->defaultTheme = config('forms-lib.theme', 'bootstrap');
    }

    /**
     * Create a new form builder instance
     */
    public function create(string $name, ?string $theme = null): FormBuilder
    {
        $theme = $theme ?? $this->defaultTheme;
        return new FormBuilder($name, $theme);
    }

    /**
     * Set default theme
     */
    public function setDefaultTheme(string $theme): self
    {
        $this->defaultTheme = $theme;
        return $this;
    }

    /**
     * Get default theme
     */
    public function getDefaultTheme(): string
    {
        return $this->defaultTheme;
    }
}

