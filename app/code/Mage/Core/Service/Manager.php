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
     * Call service method
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $context [optional]
     * @return mixed (service execution response)
     */
    public function call($serviceClass, $serviceMethod, $context = null)
    {
        // implement ACL and other routine procedures here (debugging, profiling, etc)

        $service  = $this->getService($serviceClass);

        $response = $service->$serviceMethod($context);

        return $response;
    }

    /**
     * Look up for service model
     *
     * @param string $serviceClass
     * @return Mage_Core_Service_Abstract $service
     */
    public function getService($serviceClass)
    {
        $service = $this->_objectManager->get($serviceClass);
        return $service;
    }
}
