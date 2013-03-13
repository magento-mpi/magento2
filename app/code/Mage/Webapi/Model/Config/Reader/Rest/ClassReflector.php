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
class Mage_Webapi_Model_Config_Reader_Rest_ClassReflector extends Mage_Webapi_Model_Config_Reader_ClassReflectorAbstract
{
    /** @var Mage_Webapi_Model_Config_Reader_Rest_RouteGenerator */
    protected $_routeGenerator;

    /**
     * Construct reflector with route generator.
     *
     * @param Mage_Webapi_Helper_Config $helper
     * @param Mage_Webapi_Model_Config_Reader_TypeProcessor $typeProcessor
     * @param Mage_Webapi_Model_Config_Reader_Rest_RouteGenerator $routeGenerator
     */
    public function __construct(
        Mage_Webapi_Helper_Config $helper,
        Mage_Webapi_Model_Config_Reader_TypeProcessor $typeProcessor,
        Mage_Webapi_Model_Config_Reader_Rest_RouteGenerator $routeGenerator
    ) {
        parent::__construct($helper, $typeProcessor);
        $this->_routeGenerator = $routeGenerator;
    }

    /**
     * Set types and REST routes data into reader after reflecting all files.
     *
     * @return array
     */
    public function getPostReflectionData()
    {
        return array(
            'types' => $this->_typeProcessor->getTypesData(),
            'type_to_class_map' => $this->_typeProcessor->getTypeToClassMap(),
            'rest_routes' => $this->_routeGenerator->getRoutes(),
        );
    }

    /**
     * Add REST routes to method data.
     *
     * @param Zend\Server\Reflection\ReflectionMethod $method
     * @return array
     */
    public function extractMethodData(ReflectionMethod $method)
    {
        $methodData = parent::extractMethodData($method);
        $restRoutes = $this->_routeGenerator->generateRestRoutes($method);
        $methodData['rest_routes'] = array_keys($restRoutes);

        return $methodData;
    }
}
