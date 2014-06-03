<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config;

class SourceFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create backend model by name
     *
     * @param string $modelName
     * @return mixed
     */
    public function create($modelName)
    {
        $model = $this->_objectManager->get($modelName);
        return $model;
    }
}
