<?php
/**
 * Setup model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_SetupFactory
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
     * Create setup model instance
     *
     * @param $className
     * @param array $arguments
     * @return Magento_Core_Model_Resource_SetupInterface
     * @throws LogicException
     */
    public function create($className, array $arguments = array())
    {
        $object = $this->_objectManager->create($className, $arguments);
        if (false == ($object instanceof Magento_Core_Model_Resource_SetupInterface)) {
            throw new LogicException($className . ' doesn\'t implement Magento_Core_Model_Resource_SetupInterface');
        }
        return $object;
    }
}
