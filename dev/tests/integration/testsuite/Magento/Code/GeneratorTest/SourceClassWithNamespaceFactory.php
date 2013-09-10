<?php
namespace Magento\Code\GeneratorTest;

/**
 * Factory class for Magento\Code\GeneratorTest\SourceClassWithNamespace
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
     * @var \Magento_ObjectManager
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
     * @param \Magento_ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento_ObjectManager $objectManager,
        $instanceName = 'Magento\Code\GeneratorTest\SourceClassWithNamespace'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Code\GeneratorTest\SourceClassWithNamespace
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
