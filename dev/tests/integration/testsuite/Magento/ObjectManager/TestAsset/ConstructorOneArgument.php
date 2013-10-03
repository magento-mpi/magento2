<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\TestAsset;

class ConstructorOneArgument
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_one;

    /**
     * One argument
     */

    /**
     * One argument
     *
     * @param \Magento\ObjectManager\TestAsset\Basic $one
     */
    public function __construct(
        \Magento\ObjectManager\TestAsset\Basic $one
    ) {
        $this->_one = $one;
    }
}
