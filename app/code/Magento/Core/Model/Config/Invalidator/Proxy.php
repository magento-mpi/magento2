<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Invalidator_Proxy implements Magento_Core_Model_Config_InvalidatorInterface
{
    /**
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
     * Invalidate config objects
     */
    public function invalidate()
    {
        $this->_objectManager->get('Magento_Core_Model_Config_Invalidator')->invalidate();
    }
}
