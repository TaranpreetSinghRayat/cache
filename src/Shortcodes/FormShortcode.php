<?php

namespace Tweekersnut\FormsLib\Shortcodes;

use Tweekersnut\FormsLib\Core\FormBuilder;
use Closure;

class FormShortcode
{
    /**
     * Form configuration
     */
    protected FormBuilder $form;
    protected ?Closure $successCallback = null;
    protected ?Closure $failCallback = null;
    protected ?Closure $beforeRenderCallback = null;
    protected ?Closure $afterRenderCallback = null;
    protected array $customData = [];

    /**
     * Constructor
     */
    public function __construct(FormBuilder $form)
    {
        $this->form = $form;
    }

    /**
     * Set success callback
     */
    public function onSuccess(Closure $callback): self
    {
        $this->successCallback = $callback;
        return $this;
    }

    /**
     * Set failure callback
     */
    public function onFail(Closure $callback): self
    {
        $this->failCallback = $callback;
        return $this;
    }

    /**
     * Set before render callback
     */
    public function beforeRender(Closure $callback): self
    {
        $this->beforeRenderCallback = $callback;
        return $this;
    }

    /**
     * Set after render callback
     */
    public function afterRender(Closure $callback): self
    {
        $this->afterRenderCallback = $callback;
        return $this;
    }

    /**
     * Set custom data
     */
    public function setData(array $data): self
    {
        $this->customData = $data;
        return $this;
    }

    /**
     * Get custom data
     */
    public function getData(string $key = null): mixed
    {
        if ($key === null) {
            return $this->customData;
        }
        return $this->customData[$key] ?? null;
    }

    /**
     * Handle form submission
     */
    public function handle(array $data = []): array
    {
        $data = $data ?: $_POST;

        // Validate form
        if ($this->form->validate($data)) {
            // Call success callback
            if ($this->successCallback) {
                $result = ($this->successCallback)($data, $this);
                if ($result !== null) {
                    return [
                        'success' => true,
                        'message' => 'Form submitted successfully',
                        'data' => $result
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Form submitted successfully',
                'data' => $data
            ];
        } else {
            // Call fail callback
            if ($this->failCallback) {
                $result = ($this->failCallback)($this->form->getErrors(), $this);
                if ($result !== null) {
                    return [
                        'success' => false,
                        'message' => 'Form validation failed',
                        'errors' => $this->form->getErrors(),
                        'data' => $result
                    ];
                }
            }

            // Pre-fill form with submitted data
            $this->form->values($data);

            return [
                'success' => false,
                'message' => 'Form validation failed',
                'errors' => $this->form->getErrors()
            ];
        }
    }

    /**
     * Render form
     */
    public function render(array $attributes = []): string
    {
        // Call before render callback
        if ($this->beforeRenderCallback) {
            ($this->beforeRenderCallback)($this->form, $attributes);
        }

        $html = '';

        // Add custom wrapper if provided
        if (isset($attributes['wrapper'])) {
            $html .= '<div class="' . htmlspecialchars($attributes['wrapper']) . '">';
        }

        // Render form
        $html .= $this->form->render();

        // Add custom wrapper close
        if (isset($attributes['wrapper'])) {
            $html .= '</div>';
        }

        // Call after render callback
        if ($this->afterRenderCallback) {
            $html = ($this->afterRenderCallback)($html, $this->form, $attributes);
        }

        return $html;
    }

    /**
     * Get form
     */
    public function getForm(): FormBuilder
    {
        return $this->form;
    }

    /**
     * Register as shortcode
     */
    public function registerShortcode(string $name): void
    {
        ShortcodeManager::register($name, function (array $attributes) {
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_name']) && $_POST['form_name'] === $this->form->getName()) {
                $result = $this->handle($_POST);

                if ($result['success']) {
                    return '<div class="alert alert-success">' . htmlspecialchars($result['message']) . '</div>' . $this->render($attributes);
                } else {
                    $errors = '';
                    foreach ($result['errors'] as $field => $fieldErrors) {
                        foreach ($fieldErrors as $error) {
                            $errors .= '<li>' . htmlspecialchars($error) . '</li>';
                        }
                    }
                    return '<div class="alert alert-danger"><ul>' . $errors . '</ul></div>' . $this->render($attributes);
                }
            }

            return $this->render($attributes);
        });
    }
}

