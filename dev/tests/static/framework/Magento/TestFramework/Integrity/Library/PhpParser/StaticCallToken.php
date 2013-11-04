<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Integrity\Library\PhpParser;

use Zend\Code\Reflection\ClassReflection;

/**
 * @package Magento\TestFramework
 */
class StaticCallToken
{
    /**
     * @var Tokens
     */
    protected $tokens;

    /**
     * @var UseToken
     */
    protected $useToken;

    /**
     * @var array
     */
    protected $staticCalls = array();

    /**
     * @var \ReflectionException[]
     */
    protected $exceptions = array();

    /**
     * @param Tokens $tokens
     * @param UseToken $useToken
     */
    public function __construct(Tokens $tokens, UseToken $useToken)
    {
        $this->tokens   = $tokens;
        $this->useToken = $useToken;
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isStaticCall($code)
    {
        return $code == T_PAAMAYIM_NEKUDOTAYIM;
    }

    /**
     * @param array $token
     * @return bool
     */
    protected function isTokenClass($token)
    {
        return is_array($token)
            && !(in_array($token[1], array('self', 'parent')) || preg_match('#^\$#', $token[1]));
    }

    /**
     * @param string|array $token
     * @param int $key
     */
    public function parseStaticCall($token, $key)
    {
        if ($this->tokens->hasTokenCode($token)
            && $this->isStaticCall($token[0])
            && $this->isTokenClass($this->tokens->getPreviousToken($key))
        ) {
            $this->staticCalls[] = $key;
        }
    }

    /**
     * @return array
     */
    public function getStaticCalls()
    {
        return $this->staticCalls;
    }

    /**
     * @param int $staticCall
     * @return string
     */
    public function getClassByStaticCall($staticCall)
    {
        $step = 1;
        $staticClassParts = array();
        while ($this->tokens->isString($this->tokens->getTokenCodeByKey($staticCall-$step))
            || $this->tokens->isNamespaceSeparator($this->tokens->getTokenCodeByKey($staticCall-$step))
        ) {
            $staticClassParts[] = $this->tokens->getTokenValueByKey($staticCall-$step);
            $step++;
        }
        return implode(array_reverse($staticClassParts));
    }

    /**
     * @throws \ReflectionException
     */
    public function checkDependencies()
    {
        foreach ($this->getStaticCalls() as $staticCall) {
            $class = $this->getClassByStaticCall($staticCall);
            $className = new ClassName($class);
            if ($this->useToken->hasUses() && !$className->isGlobalClass()) {
                $class = $this->useToken->prepareFullClassName($class);
            } elseif (!$className->isMagentoClass($class)) {
                continue;
            }
            try {
                new ClassReflection($class);
            } catch (\ReflectionException $e) {
                $this->exceptions[] = $e;
            }
        }
    }

    /**
     * @return \ReflectionException[]
     */
    public function getDependencies()
    {
        return $this->exceptions;
    }
}
