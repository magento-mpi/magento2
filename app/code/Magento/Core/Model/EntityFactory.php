<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_EntityFactory
{
    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param Magento_Core_Model_Abstract $className
     * @param array $data
     * @return Magento_Core_Model_Abstract
     */
    public function create(Magento_Core_Model_Abstract $className, array $data = array())
    {
        return $this->_objectManager->create($className, $data);
    }
}
