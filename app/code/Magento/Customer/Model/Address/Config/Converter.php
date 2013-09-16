<?php
/**
 * Converter of customer address format configuration from DOMDocument to array
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Customer_Model_Address_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert customer address format configuration from dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();
        /** @var DOMNodeList $formats */
        $formats = $source->getElementsByTagName('format');
        /** @var DOMNode $formatConfig */
        foreach ($formats as $formatConfig) {
            $formatCode = $formatConfig->attributes->getNamedItem('code')->nodeValue;
            $output[$formatCode] = array();
            for ($attributeIndex = 0; $attributeIndex < $formatConfig->attributes->length; $attributeIndex++) {
                $output[$formatCode][$formatConfig->attributes->item($attributeIndex)->nodeName] =
                    $formatConfig->attributes->item($attributeIndex)->nodeValue;
            }
        }
        return $output;
    }
}
