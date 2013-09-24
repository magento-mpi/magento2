<?php
/**
 *  Converter of website restrictions configuration from DOMDocument to tree array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_WebsiteRestriction_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert config
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();
        /** @var DOMNodeList $actions */
        $actions = $source->getElementsByTagName('action');
        /** @var DOMNode $actionConfig */
        foreach ($actions as $actionConfig) {
            $actionPath = $actionConfig->attributes->getNamedItem('path')->nodeValue;
            $type = $actionConfig->attributes->getNamedItem('type')->nodeValue;
            $output[$type][] = $actionPath;
        }
        return $output;
    }
}
