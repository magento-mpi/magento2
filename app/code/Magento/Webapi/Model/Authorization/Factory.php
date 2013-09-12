<?php
/**
 * API ACL Config model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Factory
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
     * Create a new instance of Magento_Webapi_Model_Authorization
     *
     * @param array $arguments fed into constructor
     * @return Magento_Webapi_Model_Authorization
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Webapi_Model_Authorization', $arguments);
    }
}