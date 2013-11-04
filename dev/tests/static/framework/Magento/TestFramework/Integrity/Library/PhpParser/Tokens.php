<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Integrity\Library\PhpParser;

/**
 * @package Magento\TestFramework
 */
class Tokens
{
    /**
     * @var array
     */
    protected $tokens = array();

    /**
     * @param string $content
     */
    public function parse($content)
    {
        $this->tokens = token_get_all($content);
    }

    /**
     * @return array
     */
    public function getAllTokens()
    {
        return $this->tokens;
    }

    /**
     * @param int $key
     * @param int $step
     * @return array
     */
    public function getPreviousToken($key, $step = 1)
    {
        return $this->tokens[$key - $step];
    }

    /**
     * @param array|string $token
     * @return bool
     */
    public function hasTokenCode($token)
    {
        return is_array($token);
    }

    /**
     * @param $key
     * @return null|int
     */
    public function getTokenCodeByKey($key)
    {
        return $this->hasTokenCode($this->tokens[$key]) ? $this->tokens[$key][0] : null;
    }

    /**
     * @param $key
     * @return string
     */
    public function getTokenValueByKey($key)
    {
        return $this->hasTokenCode($this->tokens[$key]) ? $this->tokens[$key][1] : $this->tokens[$key];
    }

    /**
     * @param int $code
     * @return bool
     */
    public function isString($code)
    {
        return $code == T_STRING;
    }

    /**
     * @param int $code
     * @return bool
     */
    public function isNamespaceSeparator($code)
    {
        return $code == T_NS_SEPARATOR;
    }
}
