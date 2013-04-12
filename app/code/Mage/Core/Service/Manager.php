<?php

class Mage_Core_Service_Manager extends Varien_Object
{
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Core_Service_Definition */
    protected $_definition;

    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Service_Definition $definition)
    {
        $this->_objectManager = $objectManager;
        $this->_definition    = $definition;
    }

    /**
     * Call service method
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $args [optional]
     * @return Mage_Core_Service_Args $args
     */
    public function call($serviceClass, $serviceMethod, $args = null)
    {
        $service  = $this->getService($serviceClass);

        $args     = $this->_definition->extractArguments($serviceClass, $serviceMethod, $args);

        $response = $service->$serviceMethod($args);

        $this->_definition->prepareResponse($serviceClass, $serviceMethod, $response, $args->getResponseSchema());

        return $response;
    }

    /**
     * Look up for service model
     *
     * @param string $serviceId
     * @return Mage_Core_Service_Abstract $service
     */
    public function getService($serviceClass)
    {
        $service = $this->_objectManager->get($serviceClass);
        return $service;
    }
}
