<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * REST API specific class reflector.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
// TODO: Refactor this class to make it an observer
class Mage_Webapi_Model_Config_Reader_Reflector_Rest
{
    /** @var Mage_Webapi_Model_Config_Reader_Reflector_Rest_RouteGenerator */
    protected $_routeGenerator;

    /**
     * Construct reflector with route generator.
     *
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Core_Service_Config_Reader_TypeProcessor $typeProcessor
     * @param Mage_Webapi_Model_Config_Reader_Reflector_Rest_RouteGenerator $routeGenerator
     */
    public function __construct(
        Mage_Webapi_Helper_Config $helper,
        Mage_Core_Service_Config_Reader_TypeProcessor $typeProcessor,
        Mage_Webapi_Model_Config_Reader_Reflector_Rest_RouteGenerator $routeGenerator
    ) {
        $this->_routeGenerator = $routeGenerator;
    }


    public function addClassReflectionData($event)
    {

    }

    /**
     * Add REST routes to method data.
     *
     * @param Varien_Event_Observer $event
     * @return array
     */
    public function addMethodReflectionData($event)
    {
        /** @var $reflectionMethod Zend\Server\Reflection\ReflectionMethod */
        $reflectionMethod = $event->getData('method_reflection');
        $restRoutes = $this->_routeGenerator->generateRestRoutes($reflectionMethod);
        /** @var $methodData Varien_Object */
        $methodData = $event->getData('method_data');
        $methodData->addData(array('rest_routes' => array_keys($restRoutes)));
    }

    /**
     * Add REST routes data into reader after reflecting all files.
     *
     * @param Varien_Event_Observer $event
     */
    public function addPostReflectionData($event)
    {
        /** @var $serviceConfig Mage_Core_Service_Config_Reader */
        $serviceConfig = $event->getData('service_config');
        $serviceConfig->addData(array('rest_routes' => $this->_routeGenerator->getRoutes()));
    }
}
