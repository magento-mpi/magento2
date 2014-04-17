<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

class UrlFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * @var string
     */
    protected $_instanceName = null;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, $instanceName = 'Magento\UrlInterface')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create Url instance with specified parameters
     *
     * @param array $data
     * @return \Magento\UrlInterface
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
