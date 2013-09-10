<?php
/**
 * Converter placeholders configuration from DOMDocument to tree array
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     * @throws InvalidArgumentException
     */
    public function convert($source)
    {
        $output = array();
        /** @var DOMNodeList $placeholder */
        $placeholder = $source->getElementsByTagName('placeholder');
        /** @var DOMNode $placeholderConfig */
        foreach ($placeholder as $placeholderConfig) {
            $placeholderCode = $placeholderConfig->attributes->getNamedItem('code')->nodeValue;
            $cacheLifeTimeNode = $placeholderConfig->attributes->getNamedItem('cacheLifeTime');
            $config = array(
                'code' => $placeholderCode,
                'cache_lifetime' => $cacheLifeTimeNode ? (int)$cacheLifeTimeNode->nodeValue : 0,
            );
            $blockInstanceName = '';
            /** @var $placeholderData DOMNode */
            foreach ($placeholderConfig->childNodes as $placeholderData) {
                if ($placeholderData->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                switch ($placeholderData->nodeName) {
                    case 'container':
                        $config['container'] = $placeholderData->attributes->getNamedItem('instance')->nodeValue;
                        break;
                    case 'block':
                        $blockInstanceName = $placeholderData->attributes->getNamedItem('instance')->nodeValue;
                        $blockNameNode = $placeholderData->attributes->getNamedItem('name');
                        if ($blockNameNode) {
                            $config['name'] = $blockNameNode->nodeValue;
                        }
                        break;
                }
            }
            $output[$blockInstanceName][] = $config;
        }
        return $output;
    }
}
