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

class Magento_ObjectManager_TestAsset_InterfaceInjection
{
    /**
     * @var Magento_ObjectManager_TestAsset_Interface
     */
    protected $_object;

    /**
     * @param Magento_ObjectManager_TestAsset_Interface $object
     */
    public function __construct(Magento_ObjectManager_TestAsset_Interface $object)
    {
        $this->_object = $object;
    }
}
