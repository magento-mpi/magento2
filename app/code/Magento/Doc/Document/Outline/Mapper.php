<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Outline;

use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class Mapper
 * @package Magento\Doc\Document\Outline
 */
class Mapper implements \Magento\Framework\Config\ConverterInterface
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
        $this->booleanUtils = $booleanUtils ?: new BooleanUtils();
    }

    /**
     * Convert configuration in DOM format to assoc array that can be used by object manager
     *
     * @param \DOMDocument $config
     * @return array
     */
    public function convert($config)
    {
        $data = [];
        /** @var \DOMNode $node */
        foreach ($config->documentElement->childNodes as $node) {
            if ($node->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $data[$node->nodeName] = $this->convertNode($node);
        }
        return $data;
    }

    public function convertNode(\DOMNode $node)
    {
        $data = [];
        $tagArguments = [];
        $tagNodeAttributes = $node->attributes;
        foreach ($tagNodeAttributes as $tagNodeAttribute) {
            /** @var \DomNode $tagNodeAttribute */
            $tagArguments[$tagNodeAttribute->nodeName] = $tagNodeAttribute->nodeValue;
        }
        $data['arguments'] = $tagArguments;

        if ($node->nodeType != XML_TEXT_NODE) {
            $data['value'] = $node->nodeValue;
        } else {
            $tagChildren = [];
            /** @var \DOMNode $tagChildNode */
            foreach ($node->childNodes as $tagChildNode) {
                if ($tagChildNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $tagChildren[$tagChildNode->nodeName] = $this->convertNode($tagChildNode);
            }
            $data['children'] = $tagChildren;
        }
        return $data;
    }
}
