<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Catalog_Model_ProductOptions_Config_Converter implements Magento_Config_ConverterInterface
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

        /** @var $optionNode DOMNode */
        foreach ($source->getElementsByTagName('option') as $optionNode) {
            $optionName = $this->_getAttributeValue($optionNode, 'name');
            $data = array();
            $data['name'] = $optionName;
            $data['label'] = $this->_getAttributeValue($optionNode, 'label');
            $data['renderer'] = $this->_getAttributeValue($optionNode, 'renderer');

            /** @var $childNode DOMNode */
            foreach ($optionNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $inputTypeName = $this->_getAttributeValue($childNode, 'name');
                $data['types'][$inputTypeName] = array(
                    'name' => $inputTypeName,
                    'label' => $this->_getAttributeValue($childNode, 'label'),
                    'disabled' => 'true' == $this->_getAttributeValue($childNode, 'disabled', 'false') ? true : false,
                );
            }
            $output[$optionName] = $data;
        }
        return $output;
    }

    /**
     * Get attribute value
     *
     * @param DOMNode $node
     * @param string $attributeName
     * @param mixed $defaultValue
     * @return null|string
     */
    protected function _getAttributeValue(DOMNode $node, $attributeName, $defaultValue = null)
    {
        $attributeNode = $node->attributes->getNamedItem($attributeName);
        $output = $defaultValue;
        if ($attributeNode) {
            $output = $attributeNode->nodeValue;
        }
        return $output;
    }
}
