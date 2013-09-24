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
        $result = array(
            'renderers' => array(),
            'totals' => array(),
        );

        $pageTypes = $source->getElementsByTagName('page');
        foreach ($pageTypes as $pageType) {
            /** @var DOMNode $pageType */
            $pageTypeName = $pageType->attributes->getNamedItem('type')->nodeValue;
            foreach ($pageType->childNodes as $rendererNode) {
                /** @var DOMNode $rendererNode */
                if ($rendererNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $productType = $rendererNode->attributes->getNamedItem('product_type')->nodeValue;
                $result['renderers'][$pageTypeName][$productType] = $rendererNode->nodeValue;
            }
        }

        $totalItems = $source->getElementsByTagName('item');
        foreach ($totalItems as $item) {
            /** @var DOMNode $item */
            $itemName = $item->attributes->getNamedItem('name')->nodeValue;
            foreach ($item->childNodes as $setting) {
                /** @var DOMNode $setting */
                if ($setting->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $result['totals'][$itemName][$setting->nodeName] = $setting->nodeValue;
            }
        }

        return $result;
    }
}
