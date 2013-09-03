<?php
namespace Magento\Code\Generator\TestAsset;

/**
 * Factory class for Magento\Code\Generator\TestAsset\SourceClassWithNamespace
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class SourceClassWithNamespaceFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\ObjectManager $objectManager, $instanceName = 'Magento\Code\Generator\TestAsset\SourceClassWithNamespace')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Code\Generator\TestAsset\SourceClassWithNamespace
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
