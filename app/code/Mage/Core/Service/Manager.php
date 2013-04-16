<?php

class Mage_Core_Service_Manager extends Varien_Object
{
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
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
}
