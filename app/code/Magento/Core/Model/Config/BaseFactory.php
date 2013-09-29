<?php
/**
 * Base config model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class BaseFactory
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
     * Create config model
     *
     * @param string|\Magento\Simplexml\Element $sourceData
     * @return \Magento\Core\Model\Config\Base
     */
    public function create($sourceData = null)
    {
        return $this->_objectManager->create('Magento\Core\Model\Config\Base', array('sourceData' => $sourceData));
    }
}
