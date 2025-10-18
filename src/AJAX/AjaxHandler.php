<?php

namespace Tweekersnut\FormsLib\AJAX;

use Tweekersnut\FormsLib\Core\FormBuilder;

class AjaxHandler
{
    protected FormBuilder $form;
    protected array $requestData = [];
    protected array $responseData = [];
    protected bool $success = false;
    protected string $message = '';
    protected array $errors = [];
    protected int $statusCode = 200;

    public function __construct(FormBuilder $form)
    {
        $this->form = $form;
        $this->requestData = $this->parseRequestData();
    }

    /**
     * Get request data from various sources
     */
    protected function parseRequestData(): array
    {
        $data = [];

        // Check for JSON input
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Fall back to POST/GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = $_GET;
        }

        return $data;
    }

    /**
     * Validate form data
     */
    public function validate(): bool
    {
        if ($this->form->validate($this->requestData)) {
            $this->success = true;
            $this->message = 'Validation passed';
            return true;
        }

        $this->success = false;
        $this->errors = $this->form->getErrors();
        $this->message = 'Validation failed';
        $this->statusCode = 422;
        return false;
    }

    /**
     * Get request data
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * Get specific field value
     */
    public function getFieldValue(string $fieldName): mixed
    {
        return $this->requestData[$fieldName] ?? null;
    }

    /**
     * Set response message
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set response data
     */
    public function setData(array $data): self
    {
        $this->responseData = array_merge($this->responseData, $data);
        return $this;
    }

    /**
     * Set success status
     */
    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Set HTTP status code
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Add error for field
     */
    public function addError(string $fieldName, string $error): self
    {
        if (!isset($this->errors[$fieldName])) {
            $this->errors[$fieldName] = [];
        }
        if (is_string($this->errors[$fieldName])) {
            $this->errors[$fieldName] = [$this->errors[$fieldName]];
        }
        $this->errors[$fieldName][] = $error;
        return $this;
    }

    /**
     * Get response as array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->responseData,
            'errors' => $this->errors,
            'form' => $this->form->toArray(),
        ];
    }

    /**
     * Get response as JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Send response
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo $this->toJson();
        exit;
    }

    /**
     * Get form field values
     */
    public function getFormData(): array
    {
        $data = [];
        foreach ($this->form->getFields() as $name => $field) {
            $data[$name] = $this->getFieldValue($name);
        }
        return $data;
    }

    /**
     * Check if request is AJAX
     */
    public static function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get request method
     */
    public static function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}

