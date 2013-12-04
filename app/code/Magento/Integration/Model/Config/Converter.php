<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Integration\Model\Config;

/**
 * Converter of integration.xml content into array format.
 */
class Converter implements \Magento\Config\ConverterInterface
{
    /**#@+
     * Array keys for config internal representation.
     */
    const KEY_EMAIL = 'email';
    const KEY_AUTHENTICATION_ENDPOINT_URL = 'endpoint_url';
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

            /** @var \DOMElement $email */
            $email = $integration->getElementsByTagName('email')->item(0)->nodeValue;
            $result[$integrationName][self::KEY_EMAIL] = $email;

            if ($integration->getElementsByTagName('endpoint_url')->length) {
                /** @var \DOMElement $endpointUrl */
                $endpointUrl = $integration->getElementsByTagName('endpoint_url')->item(0)->nodeValue;
                $result[$integrationName][self::KEY_AUTHENTICATION_ENDPOINT_URL] = $endpointUrl;
            }
        }
        return $result;
    }
}
