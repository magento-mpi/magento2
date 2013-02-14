<?php
/**
 * Setup model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_SetupFactory
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
     * Create setup model instance
     *
     * @param $className
     * @param array $arguments
     * @return Mage_Core_Model_Resource_SetupInterface
     * @throws LogicException
     */
    public function create($className, array $arguments = array())
    {
        $object = $this->_objectManager->create($className, $arguments);
        if (false == ($object instanceof Mage_Core_Model_Resource_SetupInterface)) {
            throw new LogicException($className . ' doesn\'t implement Mage_Core_Model_Resource_SetupInterface');
        }
        return $object;
    }
}
