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
            case 'dictionary':
            case 'variables':
            case 'content':
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

                            $itemDisabledNode = $itemAttributes->getNamedItem('disabled');
                            if ($itemDisabledNode) {
                                $itemData['disabled'] = $this->booleanUtils->toBoolean(
                                    $itemDisabledNode->nodeValue
                                );
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
                            throw new \Exception(
                                "Invalid application config. Unknown node: {$itemNode->nodeName}."
                            );
                    }
                }
                $output[$node->nodeName] = $data;
                break;
            case 'replace':
                foreach ($node->attributes as $itemAttribute) {
                    $data[$itemAttribute->nodeName] = $itemAttribute->nodeValue;
                }
                if ($node->childNodes) {
                    /** @var \DOMNode $childNode */
                    foreach ($node->childNodes as $childNode) {
                        if ($childNode->nodeType != XML_ELEMENT_NODE) {
                            continue;
                        }
                        $nodeName = $childNode->nodeName;
                        $data[$nodeName] = $this->convertNode($childNode);
                    }
                }
                break;
            case 'label':
            case 'description':
                $data= $node->nodeValue;
                break;
            default:
                if ($node->childNodes) {
                    foreach ($node->attributes as $attribute) {
                        $data[$attribute->nodeName] = $attribute->nodeValue;
                    }
                    $nodeData = [];
                    /** @var \DOMNode $childNode */
                    foreach ($node->childNodes as $childNode) {
                        if ($childNode->nodeType != XML_ELEMENT_NODE) {
                            continue;
                        }
                        $nodeData[$childNode->nodeName] = $this->convertNode($childNode);
                    }
                    $data[$node->nodeName] = $nodeData;
                } else {
                    $data = $node->nodeValue;
                }
                //throw new \Exception("Invalid application config. Unknown node: {$node->nodeName}.");
        }
        return $data;
    }
}
