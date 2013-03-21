<?php
/**
 * Factory class for Magento_Di_Generator_TestAsset_SourceClassWithoutNamespace
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Di_Generator_TestAsset_SourceClassWithoutNamespaceFactory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento_Di_Generator_TestAsset_SourceClassWithoutNamespace';

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
     * @return \Magento_Di_Generator_TestAsset_SourceClassWithoutNamespace
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $data);
    }
}
