<?php
/**
 * Base config model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Config;

class BaseFactory
{
    /**
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
     * Create config model
     *
     * @param string|\Magento\Simplexml\Element $sourceData
     * @return \Magento\Framework\App\Config\Base
     */
    public function create($sourceData = null)
    {
        return $this->_objectManager->create('Magento\Framework\App\Config\Base', array('sourceData' => $sourceData));
    }
}
