<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Fieldset_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * @todo update to covert data with new fieldset schema
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $fieldsets = array();
        $xpath = new DOMXPath($source);
        /** @var DOMNode $fieldset */
        foreach ($xpath->query('/config/global/fieldsets') as $fieldset) {
            $fieldsets[$fieldset->nodeName] = $this->_convert($fieldset);
        }
        return $fieldsets['fieldsets'];
    }

    /**
     * @param DOMNode $node
     * @return array|string
     */
    protected function _convert($node)
    {
        $children = array();
        if ($node->nodeType == XML_ELEMENT_NODE) {
            if ($node->hasAttributes()) {
                $attributes = $node->attributes;
                /** @var $attribute DOMNode */
                foreach ($attributes as $attribute) {
                    $children['@'][$attribute->nodeName] = $attribute->nodeValue;
                }
            }
            if ($node->hasChildNodes()) {
                foreach($node->childNodes as $childNode) {
                    $convertedChild = $this->_convert($childNode);
                    if (!empty($convertedChild))
                    {
                        if ($childNode->nodeName != '#text')
                            $children[$childNode->nodeName] = $convertedChild;
                        else
                            $children[] = $convertedChild;
                    }
                }
            }
        } elseif ($node->nodeType == XML_CDATA_SECTION_NODE
            || ($node->nodeType == XML_TEXT_NODE && trim($node->nodeValue) != '')) {
            return $node->nodeValue;
        }
        if (isset($children[0])) {
            return $children[0];
        } else {
            return $children;
        }
    }
}
