<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Webapi\Model\Config;

/**
 * Converter of webapi.xml content into array format.
 */
class Converter implements \Magento\Config\ConverterInterface
{
    /**#@+
     * Array keys for config internal representation.
     */
    const KEY_SERVICE_CLASS = 'class';
    const KEY_URL = 'url';
    const KEY_SERVICE_METHOD = 'method';
    const KEY_SECURE = 'secure';
    const KEY_ROUTES = 'routes';
    const KEY_ACL_RESOURCES = 'resources';
    const KEY_SERVICE = 'service';
    const KEY_SERVICES = 'services';
    const KEY_FORCE = 'force';
    const KEY_VALUE = 'value';
    const KEY_DATA_PARAMETERS = 'parameters';
    /**#@-*/

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function convert($source)
    {
        $result = [];
        /** @var \DOMNodeList $routes */
        $routes = $source->getElementsByTagName('route');
        /** @var \DOMElement $route */
        foreach ($routes as $route) {
            if ($route->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            /** @var \DOMElement $service */
            $service = $route->getElementsByTagName('service')->item(0);
            $serviceClass = $service->attributes->getNamedItem('class')->nodeValue;
            $serviceMethod = $service->attributes->getNamedItem('method')->nodeValue;

            $resources = $route->getElementsByTagName('resource');
            $resourceReferences = [];
            /** @var \DOMElement $resource */
            foreach ($resources as $resource) {
                if ($resource->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $ref = $resource->attributes->getNamedItem('ref')->nodeValue;
                $resourceReferences[$ref] = true;
                // For SOAP
                $result[self::KEY_SERVICES][$serviceClass][$serviceMethod][self::KEY_ACL_RESOURCES][$ref] = true;
            }

            $parameters = $route->getElementsByTagName('parameter');
            $data = [];
            /** @var \DOMElement $parameter */
            foreach ($parameters as $parameter) {
                if ($parameter->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $name = $parameter->attributes->getNamedItem('name')->nodeValue;
                $forceNode = $parameter->attributes->getNamedItem('force');
                $force = $forceNode ? (bool)$forceNode->nodeValue : false;
                $value = $parameter->nodeValue;
                $data[$name] = [
                    self::KEY_FORCE => $force,
                    self::KEY_VALUE => $value,
                ];
            }

            $method = $route->attributes->getNamedItem('method')->nodeValue;
            $url = trim($route->attributes->getNamedItem('url')->nodeValue);
            $secureNode = $route->attributes->getNamedItem('secure');
            $secure = $secureNode ? (bool)trim($secureNode->nodeValue) : false;
            // We could handle merging here by checking if the route already exists
            $result[self::KEY_ROUTES][$url][$method] = [
                self::KEY_SECURE => $secure,
                self::KEY_SERVICE => [
                    self::KEY_SERVICE_CLASS => $serviceClass,
                    self::KEY_SERVICE_METHOD => $serviceMethod,
                ],
                self::KEY_ACL_RESOURCES => $resourceReferences,
                self::KEY_DATA_PARAMETERS => $data,
            ];
            $serviceSecure = false;
            if (isset($result[self::KEY_SERVICES][$serviceClass][$serviceMethod][self::KEY_SECURE])) {
                $serviceSecure = $result[self::KEY_SERVICES][$serviceClass][$serviceMethod][self::KEY_SECURE];
            }
            $result[self::KEY_SERVICES][$serviceClass][$serviceMethod][self::KEY_SECURE] = $serviceSecure || $secure;

        }
        return $result;
    }
}
