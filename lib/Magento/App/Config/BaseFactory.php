<?php
/**
 * Base config model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config;

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
     * @return \Magento\App\Config\Base
     */
    public function create($sourceData = null)
    {
        return $this->_objectManager->create('Magento\App\Config\Base', array('sourceData' => $sourceData));
    }
}
