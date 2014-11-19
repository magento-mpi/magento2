<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration object factory
 */
namespace Magento\Backend\Model\Config;

class Factory
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
     * Create new config object
     *
     * @param array $data
     * @return \Magento\Backend\Model\Config
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Backend\Model\Config', $data);
    }
}
