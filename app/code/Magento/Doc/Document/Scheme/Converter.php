<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Scheme;

use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class Converter
 * @package Magento\Doc\Document\Scheme
 */
class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(
        BooleanUtils $booleanUtils = null
    ) {
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
        $output = array();
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
            case 'content':
            case 'resources':
                /** @var \DOMNode $itemNode */
                foreach ($node->childNodes as $itemNode) {
                    if ($itemNode->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    switch ($itemNode->nodeName) {
                        case 'item':
                            $itemData = [];

                            $itemAttributes = $itemNode->attributes;
                            foreach ($itemAttributes as $itemAttribute) {
                                $itemData[$itemAttribute->nodeName] = $itemAttribute->nodeValue;
                            }

                            $attributes = [
                                'signed-off-architect', 'signed-off-po', 'signed-off-tw'
                            ];
                            $this->fetchBooleans($itemAttributes, $attributes, $itemData);

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
                            throw new \Exception(
                                "Invalid application config. Unknown node: {$itemNode->nodeName}."
                            );
                    }
                }
                $output[$node->nodeName] = $data;
                break;
            case 'label':
            case 'description':
                $data = $node->nodeValue;
                break;
            default:
                throw new \Exception("Invalid application config. Unknown node: {$node->nodeName}.");
                break;
        }
        return $data;
    }

    /**
     * @param \DOMNamedNodeMap $itemAttributes
     * @param array $attributes
     * @param array $itemData
     */
    protected function fetchBooleans(\DOMNamedNodeMap $itemAttributes, array $attributes, array &$itemData)
    {
        foreach ($attributes as $attribute) {
            $attributeNode = $itemAttributes->getNamedItem($attribute);
            if ($attributeNode) {
                $itemData[$attribute] = $this->booleanUtils->toBoolean(
                    $attributeNode->nodeValue
                );
            }
        }
    }
}
