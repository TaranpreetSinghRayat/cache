<?php

namespace Tweekersnut\FormsLib\Validation;

use Tweekersnut\FormsLib\Fields\Field;

class Validator
{
    protected array $customRules = [];
    protected array $messages = [];

    public function __construct()
    {
        $this->initializeDefaultMessages();
    }

    /**
     * Initialize default validation messages
     */
    protected function initializeDefaultMessages(): void
    {
        $this->messages = [
            'required' => 'The {field} field is required.',
            'email' => 'The {field} must be a valid email address.',
            'numeric' => 'The {field} must be a number.',
            'phone' => 'The {field} must be a valid phone number.',
            'url' => 'The {field} must be a valid URL.',
            'date' => 'The {field} must be a valid date.',
            'datetime' => 'The {field} must be a valid date and time.',
            'min' => 'The {field} must be at least {param} characters.',
            'max' => 'The {field} must not exceed {param} characters.',
            'minvalue' => 'The {field} must be at least {param}.',
            'maxvalue' => 'The {field} must not exceed {param}.',
            'match' => 'The {field} must match {param}.',
            'unique' => 'The {field} has already been taken.',
            'custom' => 'The {field} validation failed.',
        ];
    }

    /**
     * Register custom validation rule
     */
    public function registerRule(string $name, callable $callback): self
    {
        $this->customRules[$name] = $callback;
        return $this;
    }

    /**
     * Set custom message for rule
     */
    public function setMessage(string $rule, string $message): self
    {
        $this->messages[$rule] = $message;
        return $this;
    }

    /**
     * Validate form data against fields
     */
    public function validate(array $data, array $fields): array
    {
        $errors = [];

        foreach ($fields as $name => $field) {
            $value = $data[$name] ?? null;
            $fieldErrors = $this->validateField($field, $value);
            if (!empty($fieldErrors)) {
                $errors[$name] = $fieldErrors;
            }
        }

        return $errors;
    }

    /**
     * Validate a single field
     */
    public function validateField(Field $field, mixed $value): array
    {
        $errors = [];
        $rules = $field->getRules();

        foreach ($rules as $rule) {
            $error = $this->validateRule($field, $rule, $value);
            if ($error) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * Validate a single rule
     */
    protected function validateRule(Field $field, string $rule, mixed $value): ?string
    {
        // Parse rule with parameters (e.g., "min:5")
        $parts = explode(':', $rule);
        $ruleName = strtolower($parts[0]);
        $param = $parts[1] ?? null;

        // Check custom rules first
        if (isset($this->customRules[$ruleName])) {
            $isValid = call_user_func($this->customRules[$ruleName], $value, $param, $field);
            if (!$isValid) {
                return $this->formatMessage($ruleName, $field->getLabel(), $param);
            }
            return null;
        }

        // Check built-in rules
        return match ($ruleName) {
            'required' => $this->validateRequired($value, $field),
            'email' => $this->validateEmail($value, $field),
            'numeric' => $this->validateNumeric($value, $field),
            'phone' => $this->validatePhone($value, $field),
            'url' => $this->validateUrl($value, $field),
            'date' => $this->validateDate($value, $field),
            'datetime' => $this->validateDateTime($value, $field),
            'min' => $this->validateMin($value, $param, $field),
            'max' => $this->validateMax($value, $param, $field),
            'minvalue' => $this->validateMinValue($value, $param, $field),
            'maxvalue' => $this->validateMaxValue($value, $param, $field),
            'match' => $this->validateMatch($value, $param, $field),
            default => null,
        };
    }

    protected function validateRequired(mixed $value, Field $field): ?string
    {
        if (empty($value) && $value !== '0') {
            return $this->formatMessage('required', $field->getLabel());
        }
        return null;
    }

    protected function validateEmail(mixed $value, Field $field): ?string
    {
        if (empty($value)) return null;
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->formatMessage('email', $field->getLabel());
        }
        return null;
    }

    protected function validateNumeric(mixed $value, Field $field): ?string
    {
        if (empty($value)) return null;
        if (!is_numeric($value)) {
            return $this->formatMessage('numeric', $field->getLabel());
        }
        return null;
    }

    protected function validatePhone(mixed $value, Field $field): ?string
    {
        if (empty($value)) return null;
        if (!preg_match('/^[\d\s\-\+\(\)]+$/', $value)) {
            return $this->formatMessage('phone', $field->getLabel());
        }
        return null;
    }

    protected function validateUrl(mixed $value, Field $field): ?string
    {
        if (empty($value)) return null;
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return $this->formatMessage('url', $field->getLabel());
        }
        return null;
    }

    protected function validateDate(mixed $value, Field $field): ?string
    {
        if (empty($value)) return null;
        if (!strtotime($value)) {
            return $this->formatMessage('date', $field->getLabel());
        }
        return null;
    }

    protected function validateDateTime(mixed $value, Field $field): ?string
    {
        if (empty($value)) return null;
        if (!strtotime($value)) {
            return $this->formatMessage('datetime', $field->getLabel());
        }
        return null;
    }

    protected function validateMin(mixed $value, ?string $param, Field $field): ?string
    {
        if (empty($value)) return null;
        if (strlen($value) < (int)$param) {
            return $this->formatMessage('min', $field->getLabel(), $param);
        }
        return null;
    }

    protected function validateMax(mixed $value, ?string $param, Field $field): ?string
    {
        if (empty($value)) return null;
        if (strlen($value) > (int)$param) {
            return $this->formatMessage('max', $field->getLabel(), $param);
        }
        return null;
    }

    protected function validateMinValue(mixed $value, ?string $param, Field $field): ?string
    {
        if (empty($value)) return null;
        if ((int)$value < (int)$param) {
            return $this->formatMessage('minvalue', $field->getLabel(), $param);
        }
        return null;
    }

    protected function validateMaxValue(mixed $value, ?string $param, Field $field): ?string
    {
        if (empty($value)) return null;
        if ((int)$value > (int)$param) {
            return $this->formatMessage('maxvalue', $field->getLabel(), $param);
        }
        return null;
    }

    protected function validateMatch(mixed $value, ?string $param, Field $field): ?string
    {
        if (empty($value)) return null;
        if ($value !== $param) {
            return $this->formatMessage('match', $field->getLabel(), $param);
        }
        return null;
    }

    /**
     * Format validation message
     */
    protected function formatMessage(string $rule, string $fieldLabel, ?string $param = null): string
    {
        $message = $this->messages[$rule] ?? 'Validation failed.';
        $message = str_replace('{field}', $fieldLabel, $message);
        if ($param) {
            $message = str_replace('{param}', $param, $message);
        }
        return $message;
    }
}

