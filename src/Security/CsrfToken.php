<?php

namespace Tweekersnut\FormsLib\Security;

class CsrfToken
{
    protected string $tokenName = '_token';
    protected string $sessionKey = '_csrf_token';
    protected int $tokenLength = 32;

    public function __construct(string $tokenName = '_token')
    {
        $this->tokenName = $tokenName;
        $this->sessionKey = '_csrf_token_' . $tokenName;
    }

    /**
     * Generate a new CSRF token
     */
    public function generate(): string
    {
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = bin2hex(random_bytes($this->tokenLength));
        }
        return $_SESSION[$this->sessionKey];
    }

    /**
     * Get current token
     */
    public function getToken(): string
    {
        return $_SESSION[$this->sessionKey] ?? $this->generate();
    }

    /**
     * Get token name
     */
    public function getTokenName(): string
    {
        return $this->tokenName;
    }

    /**
     * Verify token from request
     */
    public function verify(string $token): bool
    {
        $storedToken = $_SESSION[$this->sessionKey] ?? null;
        
        if (!$storedToken) {
            return false;
        }

        // Use hash_equals to prevent timing attacks
        return hash_equals($storedToken, $token);
    }

    /**
     * Verify token from POST/GET request
     */
    public function verifyRequest(): bool
    {
        $token = $_POST[$this->tokenName] ?? $_GET[$this->tokenName] ?? null;
        
        if (!$token) {
            return false;
        }

        return $this->verify($token);
    }

    /**
     * Regenerate token (useful after login)
     */
    public function regenerate(): string
    {
        unset($_SESSION[$this->sessionKey]);
        return $this->generate();
    }

    /**
     * Clear token
     */
    public function clear(): void
    {
        unset($_SESSION[$this->sessionKey]);
    }

    /**
     * Set token length
     */
    public function setTokenLength(int $length): self
    {
        $this->tokenLength = $length;
        return $this;
    }
}

