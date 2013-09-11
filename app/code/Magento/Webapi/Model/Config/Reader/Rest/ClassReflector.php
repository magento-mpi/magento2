<?php
use \Zend\Server\Reflection\ReflectionMethod;

/**
 * REST API specific class reflector.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Config\Reader\Rest;

class ClassReflector
    extends \Magento\Webapi\Model\Config\Reader\ClassReflectorAbstract
{
    /** @var \Magento\Webapi\Model\Config\Reader\Rest\RouteGenerator */
    protected $_routeGenerator;

    /**
     * Construct reflector with route generator.
     *
     * @param \Magento\Webapi\Helper\Config $helper
     * @param \Magento\Webapi\Model\Config\Reader\TypeProcessor $typeProcessor
     * @param \Magento\Webapi\Model\Config\Reader\Rest\RouteGenerator $routeGenerator
     */
    public function __construct(
        \Magento\Webapi\Helper\Config $helper,
        \Magento\Webapi\Model\Config\Reader\TypeProcessor $typeProcessor,
        \Magento\Webapi\Model\Config\Reader\Rest\RouteGenerator $routeGenerator
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
     * @param \Zend\Server\Reflection\ReflectionMethod $method
     * @return array
     */
    public function extractMethodData(\ReflectionMethod $method)
    {
        $methodData = parent::extractMethodData($method);
        $restRoutes = $this->_routeGenerator->generateRestRoutes($method);
        $methodData['rest_routes'] = array_keys($restRoutes);

        return $methodData;
    }
}
