<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Token\TokenInterface;

/*
 * Stores a token in-memory only (destroyed at end of script execution).
 */
class Memory implements TokenStorageInterface
{
    /**
     * @var object|TokenInterface
     */
    protected $tokens;

    public function __construct()
    {
        $this->tokens = [];
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service)) {
            return $this->tokens[$service];
        }

        throw new TokenNotFoundException('Token not stored');
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $this->tokens[$service] = $token;

        // allow chaining
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAccessToken($service)
    {
        return isset($this->tokens[$service]) && $this->tokens[$service] instanceof TokenInterface;
    }

    /**
     * Delete the users token. Aka, log out.
     */
    public function clearToken()
    {
        $this->tokens = [];

        // allow chaining
        return $this;
    }
}
