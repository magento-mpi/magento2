<?php
/**
 * Persistent Config Converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Persistent_Model_Persistent_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $instances = array();
        $xpath = new DOMXPath($source);
        $instances['blocks'] = $this->convertBlocks($xpath->query('/config/instances/blocks/reference'));
        return $instances;
    }

    /**
     * Convert Blocks
     *
     * @param DomNodeList $blocks
     * @return array
     */
    public function convertBlocks($blocks)
    {
        $blocksArray = array();
        foreach ($blocks as $reference) {
            $referenceAttributes = $reference->attributes;
            $id = $referenceAttributes->getNamedItem('id')->nodeValue;
            $blocksArray[$id] = array();
            /** @var $referenceSubNode DOMNode */
            foreach ($reference->childNodes as $referenceSubNode) {
                switch ($referenceSubNode->nodeName) {
                    case 'name_in_layout':
                    case 'class':
                    case 'method':
                    case 'block_type':
                        $blocksArray[$id][$referenceSubNode->nodeName] = $referenceSubNode->nodeValue;
                        break;
                    default:
                }
            }
        }
        return $blocksArray;
    }
}
