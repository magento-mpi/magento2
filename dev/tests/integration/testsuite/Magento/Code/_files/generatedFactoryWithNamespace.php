<?php
namespace Magento\Code\Generator\TestAsset;

/**
 * Factory class for Magento\Code\Generator\TestAsset\SourceClassWithNamespace
 */
class SourceClassWithNamespaceFactory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento\Code\Generator\TestAsset\SourceClassWithNamespace';

    /**
     * Object Manager instance
     *
     * @var \Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento_ObjectManager $objectManager
     */
    public function __construct(\Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Code\Generator\TestAsset\SourceClassWithNamespace
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $data);
    }
}
