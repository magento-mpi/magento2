<?php
/**
 * Factory class for Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace
 */
class Magento_Code_Generator_TestAsset_SourceClassWithoutNamespaceFactory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace';

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
     * @return \Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $data);
    }
}
