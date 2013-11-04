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
class UseToken
{
    /**
     * @var bool
     */
    protected $parseUse = false;

    /**
     * @var array
     */
    protected $uses = array();

    /**
     * @return mixed
     */
    protected function getUses()
    {
        return $this->uses;
    }

    /**
     * @return bool
     */
    public function hasUses()
    {
        return !empty($this->uses);
    }

    protected function addNewUses()
    {
        $this->uses[] = '';
    }

    /**
     * @param string $class
     * @return string
     */
    public function prepareFullClassName($class)
    {
        preg_match('#^([A-Za-z0-9_]+)(.*)$#', $class, $match);
        foreach ($this->uses as $use) {
            if (preg_match('#^([^\s]+)\s+as\s+(.*)$#', $use, $useMatch) && $useMatch[2] == $match[1]) {
                $class = $useMatch[1] . $match[2];
                break;
            }
            $packages = explode('\\', $use);
            end($packages);
            $lastPackageKey = key($packages);
            if ($packages[$lastPackageKey] == $match[1]) {
                $class = $use . $match[2];
            }
        }
        return $class;
    }

    /**
     * @param string $value
     */
    protected function appendToLastUses($value)
    {
        end($this->uses);
        $this->uses[key($this->uses)] .= trim($value);
    }

    /**
     * @return bool
     */
    protected function isParseUseInProgress()
    {
        return $this->parseUse;
    }

    protected function stopParseUse()
    {
        $this->parseUse = false;
    }

    protected function startParseUse()
    {
        $this->parseUse = true;
    }

    /**
     * @param array|string $token
     */
    public function parseUses($token)
    {
        if (is_array($token)) {
            if ($this->isParseUseInProgress()) {
                $this->appendToLastUses($token[1]);
            }
            if ($this->isUse($token[0])) {
                $this->startParseUse();
                $this->addNewUses();
            }
        } else {
            if ($this->isParseUseInProgress()) {
                if ($token == ';') {
                    $this->stopParseUse();
                }
                if ($token == ',') {
                    $this->addNewUses();
                }
            }
        }
    }

    /**
     * @param int $code
     * @return bool
     */
    protected function isUse($code)
    {
        return $code == T_USE;
    }
}
