<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_System
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Command-line options parsing class.
 */
namespace Magento\System;

class Args
{
    /**
     * @var array
     */
    public $flags;

    /**
     * @var array
     */
    public $filtered;

    /**
     * Get flags/named options
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Get filtered args
     * @return array
     */
    public function getFiltered()
    {
        return $this->filtered;
    }

    /**
     * Constructor
     * Note: the array $argv, if false $GLOBALS['argv'] is taken
     *
     * @param bool $source
     */
    public function __construct($source = false)
    {
        $this->flags = array();
        $this->filtered = array();

        if (false === $source) {
            $argv = $GLOBALS['argv'];
            array_shift($argv);
        }

        for ($i = 0,$iCount = count($argv); $i < $iCount; $i++) {
            $str = $argv[$i];

            // --foo
            if (strlen($str) > 2 && substr($str, 0, 2) == '--') {
                $str = substr($str, 2);
                $parts = explode('=', $str);
                $this->flags[$parts[0]] = true;

                // Does not have an =, so choose the next arg as its value
                if (count($parts) == 1 && isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0) {
                    $this->flags[$parts[0]] = $argv[$i + 1];
                    $argv[$i + 1] = null;
                } elseif (count($parts) == 2) {
                    $this->flags[$parts[0]] = $parts[1];
                }
            } elseif (strlen($str) == 2 && $str[0] == '-') {
                $this->flags[$str[1]] = true;
                if (isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0) {
                    $this->flags[$str[1]] = $argv[$i + 1];
                    $argv[$i + 1] = null;
                }
            } else if(!is_null($str)) {
                $this->filtered[] = $str;
            }
        }
    }
}
