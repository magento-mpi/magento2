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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create config model
     *
     * @param string|\Magento\Framework\Simplexml\Element $sourceData
     * @return \Magento\Framework\App\Config\Base
     */
    public function create($sourceData = null)
    {
        return $this->_objectManager->create('Magento\Framework\App\Config\Base', array('sourceData' => $sourceData));
    }
}
