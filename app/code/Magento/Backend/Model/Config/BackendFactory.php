<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config;

class BackendFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectmanager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }

    /**
     * Create backend model by name
     *
     * @param string $modelName
     * @return \Magento\Framework\App\Config\ValueInterface
     * @throws \InvalidArgumentException
     */
    public function create($modelName)
    {
        $model = $this->_objectManager->create($modelName);
        if (!$model instanceof \Magento\Framework\App\Config\ValueInterface) {
            throw new \InvalidArgumentException('Invalid config field backend model: ' . $modelName);
        }
        return $model;
    }
}
