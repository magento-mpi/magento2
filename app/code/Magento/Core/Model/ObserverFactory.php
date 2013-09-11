<?php
/**
 * Observer model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class ObserverFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get observer model instance
     *
     * @param string $className
     * @return \Magento\Core\Model\AbstractModel|bool
     */
    public function get($className)
    {
        return $this->_objectManager->get($className);
    }

    /**
     * Create observer model instance
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\Core\Model\AbstractModel|bool
     */
    public function create($className, array $arguments = array())
    {
        return $this->_objectManager->create($className, $arguments);
    }
}
