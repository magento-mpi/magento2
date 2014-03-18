<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\System;

/**
 * Command-line options parsing class.
 */
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
     * @param array $argv, if false $GLOBALS['argv'] is taken
     * @return void
     */
    public function __construct($source = false)
    {
        $this->flags = array();
        $this->filtered = array();

        if(false === $source) {
            $argv = $GLOBALS['argv'];
            array_shift($argv);
        }

        for($i = 0, $iCount = count($argv); $i < $iCount; $i++)
        {
            $str = $argv[$i];

            // --foo
            if(strlen($str) > 2 && substr($str, 0, 2) == '--')
            {
                $str = substr($str, 2);
                $parts = explode('=', $str);
                $this->flags[$parts[0]] = true;

                // Does not have an =, so choose the next arg as its value
                if(count($parts) == 1 && isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0)
                {
                    $this->flags[$parts[0]] = $argv[$i + 1];
                    $argv[$i + 1] = null;
                }
                elseif(count($parts) == 2) // Has a =, so pick the second piece
                {
                    $this->flags[$parts[0]] = $parts[1];
                }
            }
            elseif(strlen($str) == 2 && $str[0] == '-') // -a
            {
                $this->flags[$str[1]] = true;
                if(isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0) {
                    $this->flags[$str[1]] = $argv[$i + 1];
                    $argv[$i + 1] = null;
                }
            } else if(!is_null($str)) {
                $this->filtered[] = $str;
            }
        }
    }
}
