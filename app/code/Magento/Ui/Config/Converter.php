<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Config;

use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Data\Argument\InterpreterInterface;

/**
 * Class Converter
 */
class Converter implements ConverterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils = null)
    {
        $this->booleanUtils = $booleanUtils ? : new BooleanUtils();
    }

    /**
     * Convert configuration in DOM format to assoc array that can be used by object manager
     *
     * @param \DOMDocument $config
     * @return array
     * @throws \Exception
     */
    public function convert($config)
    {
        $output = [];
        /** @var \DOMNode $node */
        foreach ($config->documentElement->childNodes as $node) {
            if ($node->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $output[$node->nodeName] = $this->convertNode($node);
        }
        return $output;
    }

    /**
     * @param \DOMNode $node
     * @return array
     * @throws \Exception
     */
    protected function convertNode(\DOMNode $node)
    {
        $data = [];
        switch ($node->nodeName) {
            case 'elements':
                /** @var \DOMNode $itemNode */
                foreach ($node->childNodes as $itemNode) {
                    if ($itemNode->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    switch ($itemNode->nodeName) {
                        case 'element':
                            $itemData = [];
                            $itemAttributes = $itemNode->attributes;
                            foreach ($itemAttributes as $itemAttribute) {
                                $itemData[$itemAttribute->nodeName] = $itemAttribute->nodeValue;
                            }
                            if ($itemNode->childNodes) {
                                /** @var \DOMNode $childNode */
                                foreach ($itemNode->childNodes as $childNode) {
                                    if ($childNode->nodeType != XML_ELEMENT_NODE) {
                                        continue;
                                    }
                                    $nodeName = $childNode->nodeName;
                                    $itemData[$nodeName] = $this->convertNode($childNode);
                                }
                            }
                            $data[$itemAttributes->getNamedItem('name')->nodeValue] = $itemData;
                            break;
                        default:
                            throw new \Exception("Invalid application config. Unknown node: {$itemNode->nodeName}.");
                    }
                }
                $output[$node->nodeName] = $data;
                break;
            case 'settings':
                /** @var \DOMNode $itemNode */
                foreach ($node->childNodes as $itemNode) {
                    if ($itemNode->nodeType === XML_ELEMENT_NODE) {
                        $itemData = [];
                        $itemAttributes = $itemNode->attributes;
                        foreach ($itemAttributes as $itemAttribute) {
                            $itemData[$itemAttribute->nodeName] = $itemAttribute->nodeValue;
                        }
                        if ($itemNode->childNodes) {
                            /** @var \DOMNode $childNode */
                            foreach ($itemNode->childNodes as $childNode) {
                                if ($childNode->nodeType === XML_ELEMENT_NODE) {
                                    $nodeName = $childNode->nodeName;
                                    $itemData[$nodeName] = $this->convertNode($childNode);
                                }
                            }
                        }
                        $data[$itemNode->nodeName] = $itemData;
                    }
                }
                $output[$node->nodeName] = $data;
                break;
            case 'class':
            case 'extends':
            case 'content_template':
            case 'label_template':
                $data = $node->nodeValue;
                break;
            default:
                throw new \Exception("Invalid application config. Unknown node: {$node->nodeName}.");
        }

        return $data;
    }

    /**
     * Fetch booleans
     *
     * @param \DOMNamedNodeMap $itemAttributes
     * @param array $attributes
     * @param array &$itemData
     */
    protected function fetchBooleans(\DOMNamedNodeMap $itemAttributes, array $attributes, array &$itemData)
    {
        foreach ($attributes as $attribute) {
            $attributeNode = $itemAttributes->getNamedItem($attribute);
            if ($attributeNode) {
                $itemData[$attribute] = $this->booleanUtils->toBoolean($attributeNode->nodeValue);
            }
        }
    }
}
