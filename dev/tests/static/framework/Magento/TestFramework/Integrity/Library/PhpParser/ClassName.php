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
class ClassName
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @return bool
     */
    public function isMagentoClass()
    {
        return preg_match('#^\\\\Magento\\\\#', $this->class);
    }

    /**
     * @return bool
     */
    public function isGlobalClass()
    {
        return preg_match('#^\\\\#', $this->class);
    }
}
