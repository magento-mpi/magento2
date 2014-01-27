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
        $configs = array();
        $xpath = new \DOMXPath($source);

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
        foreach ($creditCards as $code=>$data) {
            $configs['credit_cards'][$code] = $data['name'];
        }

        $configs['groups'] = array();
        /** @var \DOMNode $group */
        foreach ($xpath->query('/payment/groups/group') as $group) {
            $groupAttributes = $group->attributes;
            $id = $groupAttributes->getNamedItem('id')->nodeValue;

            /** @var $groupSubNode \DOMNode */
            foreach ($group->childNodes as $groupSubNode) {
                switch ($groupSubNode->nodeName) {
                    case 'label':
                        $configs['groups'][$id] = $groupSubNode->nodeValue;
                        break;
                    default:
                        break;
                }
            }
        }

        $configs['methods'] = array();
        /** @var \DOMNode $method */
        foreach ($xpath->query('/payment/methods/method') as $method) {
            $name = $method->attributes->getNamedItem('name')->nodeValue;
            /** @var $methodSubNode \DOMNode */
            foreach ($method->childNodes as $methodSubNode) {
                if ($methodSubNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $configs['methods'][$name][$methodSubNode->nodeName] = $methodSubNode->nodeValue;
            }
        }
        return $configs;
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
}
