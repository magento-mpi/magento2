<?php
/**
 * Converter of menu hierarchy configuration from DOMDocument to tree array
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_VersionsCms_Model_Hierarchy_Config_Converter implements Magento_Config_ConverterInterface
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
        $boolAttributesNames = array('isDefault');

        /** @var DOMNodeList $menuLayouts */
        $menuLayouts = $source->getElementsByTagName('menuLayout');
        /** @var DOMNode $menuLayout */
        foreach ($menuLayouts as $menuLayout) {
            $menuLayoutName = $menuLayout->attributes->getNamedItem('name')->nodeValue;
            $menuLayoutConfig = array();
            foreach ($menuLayout->attributes as $attribute) {
                if (!in_array($attribute->nodeName, $boolAttributesNames)) {
                    $value = $attribute->nodeValue;
                } else {
                    $value = $attribute->nodeValue == "true" ? true : false;
                }
                $menuLayoutConfig[$attribute->nodeName] = $value;
            }

            /** @var DOMNode $menuLayout */
            $pageLayoutHandles = array();
            foreach ($menuLayout->getElementsByTagName('pageLayout') as $pageLayout) {
                $pageLayoutHandles[] = $pageLayout->attributes->getNamedItem('handle')->nodeValue;
            }
            $menuLayoutConfig['pageLayoutHandles'] = $pageLayoutHandles;
            $output[$menuLayoutName] = $menuLayoutConfig;
        }
        return $output;
    }
}
