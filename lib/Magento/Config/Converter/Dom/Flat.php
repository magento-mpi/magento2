<?php
/**
 * Converter that dom to array converting all attributes to general array items.
 * Examlpe:
 * <node attr="val">
 *     <subnode>val2<subnode>
 * </node>
 *
 * is converted to
 *
 * array(
 *     'node' => array(
 *         'attr' => 'wal',
 *         'subnode' => 'val2'
 *     )
 * )
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Config_Converter_Dom_Flat implements Magento_Config_ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param DOMNode $source
     * @return array
     */
    public function convert($source)
    {
        $nodeListData = array();

        /** @var $node DOMNode */
        foreach ($source->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $nodeData = array();
                /** @var $attribute DOMNode */
                foreach ($node->attributes as $attribute) {
                    if ($attribute->nodeType == XML_ATTRIBUTE_NODE) {
                        $nodeData[$attribute->nodeName] = $attribute->nodeValue;
                    }
                }
                $childrenData = $this->convert($node);

                if (is_array($childrenData)) {
                    $nodeData = array_merge($nodeData, $childrenData);
                } else {
                    $nodeData = $childrenData;
                }
                if (is_array($nodeData) && ! count($nodeData)) {
                    $nodeData = null;
                }
                if (isset($nodeListData[$node->nodeName])) {
                    if (is_array($nodeListData[$node->nodeName]) && isset($nodeListData[$node->nodeName][0])) {
                        $nodeListData[$node->nodeName][] = $nodeData;
                    } else {
                        $nodeListData[$node->nodeName] = array($nodeListData[$node->nodeName], $nodeData);
                    }
                } else {
                    $nodeListData[$node->nodeName] = $nodeData;
                }
            } elseif ($node->nodeType == XML_CDATA_SECTION_NODE
                || ($node->nodeType == XML_TEXT_NODE && trim($node->nodeValue) != '')
            ) {
                return $node->nodeValue;
            }
        }
        return $nodeListData;
    }
}
