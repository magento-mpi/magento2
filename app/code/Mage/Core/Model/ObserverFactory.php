<?php
/**
 * Observer model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_ObserverFactory
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
     * Get observer model instance
     *
     * @param string $className
     * @param array $arguments
     * @return Mage_Core_Model_Abstract|bool
     */
    public function get($className, array $arguments = array())
    {
        return $this->_objectManager->get($className, $arguments);
    }

    /**
     * Create observer model instance
     *
     * @param string $className
     * @param array $arguments
     * @return Mage_Core_Model_Abstract|bool
     */
    public function create($className, array $arguments = array())
    {
        return $this->_objectManager->create($className, $arguments);
    }
}
