<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_BackendFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectmanager
     */
    public function __construct(\Magento\ObjectManager $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }

    /**
     * Create backend model by name
     *
     * @param string $modelName
     * @return Magento_Core_Model_Config_Value
     * @throws InvalidArgumentException
     */
    public function create($modelName)
    {
        $model = $this->_objectManager->create($modelName);
        if (!$model instanceof Magento_Core_Model_Config_Value) {
            throw new InvalidArgumentException('Invalid config field backend model: ' . $modelName);
        }
        return $model;
    }
}
