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

    const KEY_IS_SECURE = 'secure';

    const KEY_HTTP_METHOD = 'httpMethod';

    const KEY_SERVICE_METHODS = 'methods';

    const KEY_METHOD_ROUTE = 'route';

    const KEY_ACL_RESOURCES = 'resources';

    /**#@-*/

    /**
     * {@inheritdoc}
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
                $result['services'][$serviceClass][$serviceMethod]['resources'][$ref] = true;
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
                    'force' => $force,
                    'value' => $value,
                ];
            }

            $method = $route->attributes->getNamedItem('method')->nodeValue;
            $url = trim($route->attributes->getNamedItem('url')->nodeValue);
            $secureNode = $route->attributes->getNamedItem('secure');
            $secure = $secureNode ? (bool)trim($secureNode->nodeValue) : false;
            // We could handle merging here by checking if the route already exists
            $result['routes'][$url][$method] = [
                'secure' => $secure,
                'service' => [
                    'class' => $serviceClass,
                    'method' => $serviceMethod,
                ],
                'resources' => $resourceReferences,
                'parameters' => $data,
            ];
            $serviceSecure = false;
            if (isset($result['services'][$serviceClass][$serviceMethod]['secure'])) {
                $serviceSecure = $result['services'][$serviceClass][$serviceMethod]['secure'];
            }
            $result['services'][$serviceClass][$serviceMethod]['secure'] = $serviceSecure || $secure;

        }
        return $result;
    }
}
