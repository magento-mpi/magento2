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
class ThrowToken
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
     * @var \ReflectionException[]
     */
    protected $exceptions = array();

    /**
     * @var array
     */
    protected $throws = array();

    /**
     * @param Tokens $tokens
     * @param UseToken $useToken
     */
    public function __construct(Tokens $tokens, UseToken $useToken)
    {
        $this->tokens = $tokens;
        $this->useToken = $useToken;
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isThrow($code)
    {
        return $code == T_THROW;
    }

    /**
     * @param array|string $token
     * @param $key
     */
    public function parseThrows($token, $key)
    {
        if ($this->tokens->hasTokenCode($token) && $this->isThrow($token[0])) {
            $this->throws[] = $key;
        }
    }

    /**
     * @return array
     */
    public function getThrows()
    {
        return $this->throws;
    }

    public function checkDependencies()
    {
        foreach ($this->getThrows() as $throw) {
            $class = '';
            if ($this->tokens->getTokenCodeByKey($throw + 2) == T_NEW) {
                $step = 4;
                while ($this->tokens->isString($this->tokens->getTokenCodeByKey($throw+$step))
                    || $this->tokens->isNamespaceSeparator($this->tokens->getTokenCodeByKey($throw+$step))
                ) {
                    $class .= trim($this->tokens->getTokenValueByKey($throw + $step));
                    $step++;
                }

                if ($this->useToken->hasUses() && !(new ClassName($class))->isGlobalClass()) {
                    $class = $this->useToken->prepareFullClassName($class);
                }

                try {
                    new ClassReflection($class);
                } catch (\ReflectionException $e) {
                    $this->exceptions[] = $e;
                }
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
