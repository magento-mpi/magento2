<?php
/**
 * Payment Config Converter
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Payment_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $configs = array();
        $xpath = new DOMXPath($source);

        $configs['credit_cards'] = array();
        /** @var DOMNode $tyep */
        foreach ($xpath->query('/payment/credit_cards/type') as $type) {
            $typeArray = array();

            /** @var $typeSubNode DOMNode */
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
            $code = $typeAttributes->getNamedItem('code')->nodeValue;
            $configs['credit_cards'][$code] = $typeArray;
        }

        $configs['groups'] = array();
        /** @var DOMNode $group */
        foreach ($xpath->query('/payment/groups/group') as $group) {
            $groupAttributes = $group->attributes;
            $id = $groupAttributes->getNamedItem('id')->nodeValue;

            /** @var $groupSubNode DOMNode */
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
        return $configs;
    }
}
