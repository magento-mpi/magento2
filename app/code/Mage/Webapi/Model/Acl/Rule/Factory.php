<?php
/**
 * ACL Role factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Acl_Rule_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Mage_Webapi_Model_Acl_Rule
     *
     * @param array $arguments fed into constructor
     * @return Mage_Webapi_Model_Acl_Rule
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Webapi_Model_Acl_Rule', $arguments);
    }
}
