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

class BasicInjection
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_object;

    /**
     * @param \Magento\ObjectManager\TestAsset\Basic $object
     */
    public function __construct(\Magento\ObjectManager\TestAsset\Basic $object)
    {
        $this->_object = $object;
    }
}
