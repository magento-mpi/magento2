<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Webapi\Model\Config\Integration;

/**
 * Converter of api.xml content into array format.
 */
class Converter implements \Magento\Config\ConverterInterface
{
    /**#@+
     * Array keys for config internal representation.
     */
    const API_RESOURCES = 'resources';
    const API_RESOURCE_NAME = 'name';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $result = array();
        /** @var \DOMNodeList $integrations */
        $integrations = $source->getElementsByTagName('integration');
        /** @var \DOMElement $integration */
        foreach ($integrations as $integration) {
            if ($integration->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $integrationName = $integration->attributes->getNamedItem('name')->nodeValue;
            $result[$integrationName] = array();
            $result[$integrationName][self::API_RESOURCES] = array();
            /** @var \DOMNodeList $resources */
            $resources = $integration->getElementsByTagName('resource');
            /** @var \DOMElement $resource */
            foreach ($resources as $resource) {
                if ($resource->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $resource = $resource->attributes->getNamedItem('name')->nodeValue;
                $result[$integrationName][self::API_RESOURCES][] = $resource;
            }
        }
        return $result;
    }
}