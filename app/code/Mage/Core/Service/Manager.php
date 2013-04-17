<?php

class Mage_Core_Service_Manager extends Varien_Object
{
    const AREA_SERVICES = 'services';

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /**
     * Access Control List builder
     *
     * @var Mage_Core_Model_Acl_Builder
     */
    protected $_aclBuilder;

    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Model_Acl_Builder $aclBuilder)
    {
        $this->_objectManager = $objectManager;
        $this->_aclBuilder = $aclBuilder;
    }

    /**
     * Call a service method
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $context [optional]
     * @return mixed (service execution response)
     */
    public function call($serviceClass, $serviceMethod, $context = null)
    {
        $service  = $this->getService($serviceClass);

        $response = $service->call($serviceMethod, $context);

        return $response;
    }

    /**
     * Retrieve a service instance
     *
     * @param string $serviceClass
     * @return Mage_Core_Service_Type_Abstract $service
     */
    public function getService($serviceClass)
    {
        $service = $this->_objectManager->get($serviceClass);
        return $service;
    }

    /**
     * @return Magento_Acl
     */
    public function getAcl()
    {
        $acl = $this->_aclBuilder->getAcl(self::AREA_SERVICES);
        return $acl;
    }

    /**
     * @return Mage_User_Model_User
     */
    public function getUser()
    {
        // @toto remove stub
        $user = Mage::getSingleton('Mage_User_Model_User')->setUserId(1);
        return $user;
    }
}
