<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config;

class BackendFactory
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
     * @return \Magento\Core\Model\Config\Value
     * @throws \InvalidArgumentException
     */
    public function create($modelName)
    {
        $model = $this->_objectManager->create($modelName);
        if (!$model instanceof \Magento\Core\Model\Config\Value) {
            throw new \InvalidArgumentException('Invalid config field backend model: ' . $modelName);
        }
        return $model;
    }
}
