<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Cache\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();
        /** @var \DOMNodeList $types */
        $types = $source->getElementsByTagName('type');
        /** @var \DOMNode $type */
        foreach ($types as $type) {
            $typeConfig = array();
            foreach ($type->attributes as $attribute) {
                $typeConfig[$attribute->nodeName] = $attribute->nodeValue;
            }
            /** @var \DOMNode $childNode */
            foreach ($type->childNodes as $childNode) {
                if ($childNode->nodeType == XML_ELEMENT_NODE
                    || ($childNode->nodeType == XML_CDATA_SECTION_NODE
                    || ($childNode->nodeType == XML_TEXT_NODE && trim($childNode->nodeValue) != ''))
                ) {
                    $typeConfig[$childNode->nodeName] = $childNode->nodeValue;
                }
            }
            $output[$type->attributes->getNamedItem('name')->nodeValue] = $typeConfig;
        }
        return array('types' => $output);
    }
}
