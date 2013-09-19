<?php
/**
 * Converter of pdf configuration from DOMDocument to array
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Sales_Model_Order_Pdf_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $pageTypes = $source->getElementsByTagName('page');

        /** @var DOMNode $pageType */
        foreach ($pageTypes as $pageType) {
            $pageTypeName = $pageType->attributes->getNamedItem('type')->nodeValue;
            foreach ($pageType->childNodes as $rendererNode) {
                if ($rendererNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $productType = $rendererNode->attributes->getNamedItem('product_type')->nodeValue;
                $return['renderers'][$pageTypeName][$productType] = $rendererNode->nodeValue;
            }
        }
        return $return;
    }
}
