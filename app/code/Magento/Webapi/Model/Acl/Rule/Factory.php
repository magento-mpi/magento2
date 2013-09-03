<?php
/**
 * ACL Role factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Rule_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Magento_Webapi_Model_Acl_Rule
     *
     * @param array $arguments fed into constructor
     * @return Magento_Webapi_Model_Acl_Rule
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Webapi_Model_Acl_Rule', $arguments);
    }
}
