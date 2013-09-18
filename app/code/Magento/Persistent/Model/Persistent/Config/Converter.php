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
            $widgetArray = array('@' => array());
            $id = $referenceAttributes->getNamedItem('id')->nodeValue;
            $referenceArray = array();
            /** @var $referenceSubNode DOMNode */
            foreach ($reference->childNodes as $referenceSubNode) {
                switch ($referenceSubNode->nodeName) {
                    case 'name_in_layout':
                        $referenceArray[$referenceSubNode->nodeName] = $referenceSubNode->nodeValue;
                        break;
                    case 'class':
                        $referenceArray[$referenceSubNode->nodeName] = $referenceSubNode->nodeValue;
                        break;
                    case 'method':
                        $referenceArray[$referenceSubNode->nodeName] = $referenceSubNode->nodeValue;
                        break;
                    case 'block_type':
                        $referenceArray[$referenceSubNode->nodeName] = $referenceSubNode->nodeValue;
                        break;
                    case "#text":
                        break;
                    case '#comment':
                        break;
                    default:
                        throw new LogicException(
                            sprintf(
                                "Unsupported child xml node '%s' found in the 'reference' node",
                                $referenceSubNode->nodeName
                            )
                        );
                }
            }
            $blocksArray[$id] = $referenceArray;
        }
        return $blocksArray;
    }
}
