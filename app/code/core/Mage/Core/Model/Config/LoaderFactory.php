<?php
/**
 * Application config loader factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_LoaderFactory
{
    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create loader
     *
     * @param string $type
     * @return Mage_Core_Model_Config_LoaderInterface
     */
    public function create($type)
    {
        return $this->_objectManager->create($type);
    }
}
