<?php
/**
 * Payment Config Converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Payment\Model\Config;

class Converter implements \Magento\Config\ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $xpath = new \DOMXPath($source);
        return array(
            'credit_cards' => $this->convertCreditCards($xpath),
            'groups' => $this->convertGroups($xpath),
            'methods' => $this->convertMethods($xpath),
        );
    }

    /**
     * Convert credit cards xml tree to array
     *
     * @param \DOMXPath $xpath
     * @return array
     */
    protected function convertCreditCards(\DOMXPath $xpath)
    {
        $creditCards = array();
        /** @var \DOMNode $type */
        foreach ($xpath->query('/payment/credit_cards/type') as $type) {
            $typeArray = array();

            /** @var $typeSubNode \DOMNode */
            foreach ($type->childNodes as $typeSubNode) {
                switch ($typeSubNode->nodeName) {
                    case 'label':
                        $typeArray['name'] = $typeSubNode->nodeValue;
                        break;
                    default:
                        break;
                }
            }

            $typeAttributes = $type->attributes;
            $typeArray['order'] = $typeAttributes->getNamedItem('order')->nodeValue;
            $ccId = $typeAttributes->getNamedItem('id')->nodeValue;
            $creditCards[$ccId] = $typeArray;
        }
        uasort($creditCards, array($this, '_compareCcTypes'));
        $config = array();
        foreach ($creditCards as $code=>$data) {
            $config[$code] = $data['name'];
        }
        return $config;
    }

    /**
     * Compare sort order of CC Types
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod) Used in callback.
     *
     * @param array $left
     * @param array $right
     * @return int
     */
    private function _compareCcTypes($left, $right)
    {
        return $left['order'] - $right['order'];
    }

    /**
     * Convert groups xml tree to array
     *
     * @param \DOMXPath $xpath
     * @return array
     */
    protected function convertGroups(\DOMXPath $xpath)
    {
        $config = array();
        /** @var \DOMNode $group */
        foreach ($xpath->query('/payment/groups/group') as $group) {
            $groupAttributes = $group->attributes;
            $id = $groupAttributes->getNamedItem('id')->nodeValue;

            /** @var $groupSubNode \DOMNode */
            foreach ($group->childNodes as $groupSubNode) {
                switch ($groupSubNode->nodeName) {
                    case 'label':
                        $config[$id] = $groupSubNode->nodeValue;
                        break;
                    default:
                        break;
                }
            }
        }
        return $config;
    }

    /**
     * Convert methods xml tree to array
     *
     * @param \DOMXPath $xpath
     * @return array
     */
    protected function convertMethods(\DOMXPath $xpath)
    {
        $config = array();
        /** @var \DOMNode $method */
        foreach ($xpath->query('/payment/methods/method') as $method) {
            $name = $method->attributes->getNamedItem('name')->nodeValue;
            /** @var $methodSubNode \DOMNode */
            foreach ($method->childNodes as $methodSubNode) {
                if ($methodSubNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $config[$name][$methodSubNode->nodeName] = $methodSubNode->nodeValue;
            }
        }
        return $config;
    }
}
