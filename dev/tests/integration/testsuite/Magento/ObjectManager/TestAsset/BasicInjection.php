<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\ObjectManager
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_TestAsset_BasicInjection
{
    /**
     * @var Magento_ObjectManager_TestAsset_Basic
     */
    protected $_object;

    /**
     * @param Magento_ObjectManager_TestAsset_Basic $object
     */
    public function __construct(Magento_ObjectManager_TestAsset_Basic $object)
    {
        $this->_object = $object;
    }
}
