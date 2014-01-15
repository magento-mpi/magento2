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
     */
    public function convert($source)
    {
        $output = array();
        $xpath = new \DOMXPath($source);
        $views = $xpath->evaluate('/config/view');
        /** @var $viewNode \DOMNode */
        foreach ($views as $viewNode) {
            $data = array();
            $viewId = $this->getAttributeValue($viewNode, 'id');
            $data['view_id'] = $viewId;
            $data['action_class'] = $this->getAttributeValue($viewNode, 'class');
            $data['subscriptions'] = array();

            /** @var $childNode \DOMNode */
            foreach ($viewNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }

                $data = $this->convertChild($childNode, $data);
            }
            $output[$viewId] = $data;
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
            case 'subscriptions':
                /** @var $subscription \DOMNode */
                foreach ($childNode->childNodes as $subscription) {
                    if ($subscription->nodeType != XML_ELEMENT_NODE || $subscription->nodeName != 'table') {
                        continue;
                    }
                    $name = $this->getAttributeValue($subscription, 'name');
                    $column = $this->getAttributeValue($subscription, 'entity_column');
                    $data['subscriptions'][$name] = array(
                        'name' => $name,
                        'column' => $column,
                    );
                }
                break;
        }
        return $data;
    }
}
