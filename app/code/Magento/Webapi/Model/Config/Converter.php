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
    const KEY_BASE_URL = 'baseUrl';
    const KEY_SERVICE_METHOD = 'method';
    const KEY_IS_SECURE = 'isSecure';
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
        $result = array();
        /** @var \DOMNodeList $services */
        $services = $source->getElementsByTagName('service');
        /** @var \DOMElement $service */
        foreach ($services as $service) {
            if ($service->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $serviceClass = $service->attributes->getNamedItem('class')->nodeValue;
            $result[$serviceClass] = array(
                self::KEY_SERVICE_CLASS => $serviceClass,
                self::KEY_SERVICE_METHODS => array()
            );

            /** @var \DOMAttr $baseUrlNode */
            $baseUrlNode = $service->attributes->getNamedItem('baseUrl');
            if ($baseUrlNode) {
                $result[$serviceClass][self::KEY_BASE_URL] = $baseUrlNode->nodeValue;
            }

            /** @var \DOMNodeList $restRoutes */
            $restRoutes = $service->getElementsByTagName('rest-route');
            /** @var \DOMElement $restRoute */
            foreach ($restRoutes as $restRoute) {
                if ($restRoute->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $httpMethod = $restRoute->attributes->getNamedItem('httpMethod')->nodeValue;
                $method = $restRoute->attributes->getNamedItem('method')->nodeValue;

                $resources = $restRoute->attributes->getNamedItem('resources')->nodeValue;
                /** Allow whitespace usage after comma. */
                $resources = str_replace(', ', ',', $resources);
                $resources = explode(',', $resources);

                $isSecureAttribute = $restRoute->attributes->getNamedItem('isSecure');
                $isSecure = $isSecureAttribute ? true : false;
                $path = (string)$restRoute->nodeValue;

                $result[$serviceClass][self::KEY_SERVICE_METHODS][$method] = array(
                    self::KEY_HTTP_METHOD => $httpMethod,
                    self::KEY_SERVICE_METHOD => $method,
                    self::KEY_METHOD_ROUTE => $path,
                    self::KEY_IS_SECURE => $isSecure,
                    self::KEY_ACL_RESOURCES => $resources
                );
            }
        }
        return $result;
    }
}
