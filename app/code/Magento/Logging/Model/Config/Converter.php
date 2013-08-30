<?php
/**
 * Logging configuration Converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Enterprise_Logging_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $result = array();
        $xpath = new DOMXPath($source);
        /** @var DOMNode $fieldset */
        foreach ($xpath->query('/config/scope') as $log) {

        }
        return $result;
    }
}