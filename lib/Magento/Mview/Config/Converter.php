<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mview\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source)
    {
        $output = array();
        $xpath = new \DOMXPath($source);
        $indexers = $xpath->evaluate('/config/view');
        /** @var $typeNode \DOMNode */
        foreach ($indexers as $indexerNode) {
            $data = array();
            $indexerId = $this->_getAttributeValue($indexerNode, 'id');
            $data['view_id'] = $indexerId;
            $data['action_class'] = $this->_getAttributeValue($indexerNode, 'class');
            $data['subscriptions'] = array();

            /** @var $childNode \DOMNode */
            foreach ($indexerNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                switch ($childNode->nodeName) {
                    case 'subscriptions':
                        /** @var $subscription \DOMNode */
                        foreach ($childNode->childNodes as $subscription) {
                            if ($subscription->nodeType != XML_ELEMENT_NODE || $subscription->nodeName != 'table') {
                                continue;
                            }
                            $name = $this->_getAttributeValue($subscription, 'name');
                            $column = $this->_getAttributeValue($subscription, 'entity_column');
                            $data['subscriptions'][$name] = array(
                                'name' => $name,
                                'column' => $column,
                            );
                        }
                        break;
                }
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
    protected function _getAttributeValue(\DOMNode $input, $attributeName, $default = null)
    {
        $node = $input->attributes->getNamedItem($attributeName);
        return $node ? $node->nodeValue : $default;
    }
}
