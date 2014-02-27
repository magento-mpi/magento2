<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Rest;

use Magento\Webapi\Controller\Rest\Router\Route;
use \Magento\Webapi\Model\Config\Converter;
use Magento\Webapi\Model\Config as ModelConfig;

/**
 * Webapi Config Model for Rest.
 */
class Config
{
    /**#@+
     * HTTP methods supported by REST.
     */
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_POST = 'POST';
    /**#@-*/

    /**#@+
     * Keys that a used for config internal representation.
     */
    const KEY_IS_SECURE = 'isSecure';
    const KEY_CLASS = 'class';
    const KEY_METHOD = 'method';
    const KEY_ROUTE_PATH = 'routePath';
    const KEY_ACL_RESOURCES = 'resources';
    /*#@-*/

    /** @var ModelConfig */
    protected $_config;

    /** @var \Magento\Controller\Router\Route\Factory */
    protected $_routeFactory;

    /**
     * @param ModelConfig $config
     * @param \Magento\Controller\Router\Route\Factory $routeFactory
     */
    public function __construct(
        ModelConfig $config,
        \Magento\Controller\Router\Route\Factory $routeFactory
    ) {
        $this->_config = $config;
        $this->_routeFactory = $routeFactory;
    }

    /**
     * Create route object.
     *
     * @param array $routeData Expected format:
     *  <pre>array(
     *      'routePath' => '/categories/:categoryId',
     *      'class' => 'Magento\Catalog\Service\CategoryService',
     *      'serviceMethod' => 'item'
     *      'secure' => true
     *  );</pre>
     * @return \Magento\Webapi\Controller\Rest\Router\Route
     */
    protected function _createRoute($routeData)
    {
        /** @var $route \Magento\Webapi\Controller\Rest\Router\Route */
        $route = $this->_routeFactory->createRoute(
            'Magento\Webapi\Controller\Rest\Router\Route',
            $this->_getRoutePath($routeData[self::KEY_ROUTE_PATH])
        );

        $route->setServiceClass($routeData[self::KEY_CLASS])
            ->setServiceMethod($routeData[self::KEY_METHOD])
            ->setSecure($routeData[self::KEY_IS_SECURE])
            ->setAclResources($routeData[self::KEY_ACL_RESOURCES]);
        return $route;
    }

    /**
     * Lowercase all parts of the given route path except for the path parameters.
     *
     * @param string $routePath The route path (e.g. '/V1/Categories/:categoryId')
     * @return string The modified route path (e.g. '/v1/categories/:categoryId')
     */
    protected function _getRoutePath($routePath)
    {
        $routePathParts = explode('/', $routePath);
        $pathParts = [];
        foreach ($routePathParts as $pathPart) {
            $pathParts[] = (substr($pathPart, 0, 1) === ":") ? $pathPart : strtolower($pathPart);
        }
        return implode('/', $pathParts);
    }

    /**
     * Get service base URL
     *
     * @param \Magento\Webapi\Controller\Rest\Request $request
     * @return string|null
     */
    protected function _getServiceBaseUrl($request)
    {
        $baseUrlRegExp = '#^/?\w+/\w+#';
        $serviceBaseUrl = preg_match($baseUrlRegExp, $request->getPathInfo(), $matches) ? $matches[0] : null;

        return $serviceBaseUrl;
    }

    /**
     * TODO: Refactor $this->_config->getServices() to return array with baseUrl as the key since its unique and
     *       needs to be used directly instead of looping each key
     * Generate the list of available REST routes. Current HTTP method is taken into account.
     *
     * @param \Magento\Webapi\Controller\Rest\Request $request
     * @return Route[]
     * @throws \Magento\Webapi\Exception
     */
    public function getRestRoutes(\Magento\Webapi\Controller\Rest\Request $request)
    {
        $serviceBaseUrl = $this->_getServiceBaseUrl($request);
        $httpMethod = $request->getHttpMethod();
        $routes = array();
        foreach ($this->_config->getServices() as $serviceName => $serviceData) {
            // skip if baseurl is not null and does not match
            if (!isset($serviceData[Converter::KEY_BASE_URL]) || !$serviceBaseUrl
                || strcasecmp(trim($serviceBaseUrl, '/'), trim($serviceData[Converter::KEY_BASE_URL], '/')) !== 0
            ) {
                // baseurl does not match, just skip this service
                continue;
            }
            foreach ($serviceData[Converter::KEY_SERVICE_METHODS] as $methodName => $methodInfo) {
                if (strtoupper($methodInfo[Converter::KEY_HTTP_METHOD]) == strtoupper($httpMethod)) {
                    $secure = $methodInfo[Converter::KEY_IS_SECURE];
                    $methodRoute = $methodInfo[Converter::KEY_METHOD_ROUTE];
                    $aclResources = $methodInfo[Converter::KEY_ACL_RESOURCES];
                    $routes[] = $this->_createRoute(
                        array(
                            self::KEY_ROUTE_PATH => $serviceData[Converter::KEY_BASE_URL] . $methodRoute,
                            self::KEY_CLASS => $serviceName,
                            self::KEY_METHOD => $methodName,
                            self::KEY_IS_SECURE => $secure,
                            self::KEY_ACL_RESOURCES => $aclResources
                        )
                    );
                }
            }
        }

        return $routes;
    }
}
