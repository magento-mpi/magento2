<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Indexer\Model\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = array();
        $xpath = new \DOMXPath($source);
        $indexers = $xpath->evaluate('/config/indexer');
        /** @var $typeNode \DOMNode */
        foreach ($indexers as $indexerNode) {
            $data = array();
            $indexerId = $this->getAttributeValue($indexerNode, 'id');
            $data['id'] = $indexerId;
            $data['view_id'] = $this->getAttributeValue($indexerNode, 'view_id');
            $data['class'] = $this->getAttributeValue($indexerNode, 'class');
            $data['title'] = '';
            $data['title_translate'] = '';
            $data['description'] = '';
            $data['description_translate'] = '';

            /** @var $childNode \DOMNode */
            foreach ($indexerNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                $data = $this->convertChild($childNode, $data);
            }
            $output[$indexerId] = $data;
        }
        return $output;
    }

    /**
     * Get attribute value
     *
     * @param \DOMNode $input
     * @param string $attributeName
     * @param mixed $default
     * @return null|string
     */
    protected function getAttributeValue(\DOMNode $input, $attributeName, $default = null)
    {
        $node = $input->attributes->getNamedItem($attributeName);
        return $node ? $node->nodeValue : $default;
    }

    /**
     * Convert child from dom to array
     *
     * @param \DOMNode $childNode
     * @param array $data
     * @return array
     */
    protected function convertChild(\DOMNode $childNode, $data)
    {
        switch ($childNode->nodeName) {
            case 'title':
                $data['title'] = $childNode->nodeValue;
                $data['title_translate'] = $this->getAttributeValue($childNode, 'translate');
                break;
            case 'description':
                $data['description'] = $childNode->nodeValue;
                $data['description_translate'] = $this->getAttributeValue($childNode, 'translate');
                break;
        }
        return $data;
    }
}
