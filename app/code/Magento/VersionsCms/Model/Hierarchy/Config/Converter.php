<?php
/**
 * Converter of menu hierarchy configuration from \DOMDocument to tree array
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Model\Hierarchy\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = [];
        $boolAttributesNames = ['isDefault'];

        /** @var \DOMNodeList $menuLayouts */
        $menuLayouts = $source->getElementsByTagName('menuLayout');
        /** @var DOMNode $menuLayout */
        foreach ($menuLayouts as $menuLayout) {
            $menuLayoutName = $menuLayout->attributes->getNamedItem('name')->nodeValue;
            $menuLayoutConfig = [];
            foreach ($menuLayout->attributes as $attribute) {
                if (!in_array($attribute->nodeName, $boolAttributesNames)) {
                    $value = $attribute->nodeValue;
                } else {
                    $value = $attribute->nodeValue == "true" ? true : false;
                }
                $menuLayoutConfig[$attribute->nodeName] = $value;
            }

            /** @var DOMNode $menuLayout */
            $pageLayoutHandles = [];
            foreach ($menuLayout->getElementsByTagName('pageLayout') as $pageLayout) {
                $pageLayoutHandles[] = $pageLayout->attributes->getNamedItem('handle')->nodeValue;
            }
            $menuLayoutConfig['pageLayoutHandles'] = $pageLayoutHandles;
            $output[$menuLayoutName] = $menuLayoutConfig;
        }
        return $output;
    }
}
