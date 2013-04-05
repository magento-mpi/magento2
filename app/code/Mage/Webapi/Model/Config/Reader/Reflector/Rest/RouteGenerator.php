<?php
use Zend\Server\Reflection\ReflectionMethod;

/**
 * REST routes generator.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Config_Reader_Reflector_Rest_RouteGenerator
{
    const ANNOTATION_HTTP_PATH = 'Path';
    const ANNOTATION_HTTP_METHOD = 'Method';

    /** @var array */
    protected $_routes = array();

    /**
     * @var Mage_Webapi_Helper_Config
     */
    protected $_helper;

    /**
     * Construct routes generator.
     *
     * @param Mage_Webapi_Helper_Config $helper
     */
    public function __construct(Mage_Webapi_Helper_Config $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Generate a list of routes available fo the specified method.
     *
     * @param ReflectionMethod $methodReflection
     * @return array
     * @throws LogicException
     */
    public function generateRestRoutes(ReflectionMethod $methodReflection)
    {
        $routes = array();
        $classReflection = $methodReflection->getDeclaringClass();
        $servicePath = $this->_helper->getAnnotationValue(
            $classReflection,
            self::ANNOTATION_HTTP_PATH
        );
        if (!is_string($servicePath)) {
            throw new LogicException(sprintf(
                    'Service "%s" must have "@%s" annotation defined.',
                    $classReflection->getName(),
                    self::ANNOTATION_HTTP_PATH
                )
            );
        }
        $methodPath = $this->_helper->getAnnotationValue($methodReflection, self::ANNOTATION_HTTP_PATH);
        $methodPath = is_null($methodPath) ? '' : $methodPath;
        if (!is_string($methodPath)) {
            throw new LogicException(sprintf(
                    'Method "%s" of "%s" class must have "@%s" annotation defined.',
                    $methodReflection->getName(),
                    $classReflection->getName(),
                    self::ANNOTATION_HTTP_PATH
                )
            );
        }
        $httpMethod = $this->_helper->getAnnotationValue($methodReflection, self::ANNOTATION_HTTP_METHOD);
        if (!is_string($httpMethod)) {
            throw new LogicException(sprintf(
                    'Method "%s" of "%s" class must have "@%s" annotation defined.',
                    $methodReflection->getName(),
                    $classReflection->getName(),
                    self::ANNOTATION_HTTP_METHOD
                )
            );
        }
        $routePath = $servicePath . $methodPath;
        $serviceName = $this->_helper->translateServiceName($classReflection->getName());
        // TODO: Remove $routes array usage if only one route should be supported
        $routes[$routePath] = array(
            'httpMethod' => $httpMethod,
            'methodName' => $methodReflection->getName(),
            'serviceName' => $serviceName
        );

        $this->_routes = array_merge($this->_routes, $routes);
        return $routes;
    }

    /**
     * Retrieve all generated routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }
}
