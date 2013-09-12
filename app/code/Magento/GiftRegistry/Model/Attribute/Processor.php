<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry custom attribute processor model
 */
class Magento_GiftRegistry_Model_Attribute_Processor extends Magento_Core_Model_Abstract
{
    const XML_PROTOTYPE_NODE  = 'prototype';
    const XML_REGISTRY_NODE   = 'registry';
    const XML_REGISTRANT_NODE = 'registrant';

    /**
     * Convert attributes data to xml
     *
     * @param Magento_GiftRegistry_Model_Type $type
     * @return string
     */
    public function processData($type)
    {
        $data = $type->getAttributes();
        if ($data) {
            $xmlObj = new Magento_Simplexml_Element('<config></config>');
            $typeXml = $xmlObj->addChild(self::XML_PROTOTYPE_NODE);
            if (is_array($data)) {
                $groups = array();
                foreach ($data as $attributes) {
                    foreach ($attributes as $attribute) {
                        if ($attribute['group'] == self::XML_REGISTRANT_NODE) {
                            $group = self::XML_REGISTRANT_NODE;
                        } else {
                            $group = self::XML_REGISTRY_NODE;
                        }
                        $groups[$group][$attribute['code']] = $attribute;
                    }
                }
                foreach ($groups as $group => $attributes) {
                    $this->processDataType($typeXml, $group, $attributes);
                }
            }
            return $xmlObj->asNiceXml();
        }
    }

    /**
     * Process attribute types as xml
     *
     * @param Magento_Simplexml_Element $typeXml
     * @param string $group
     * @return array $attributes
     */
    public function processDataType($typeXml, $group, $attributes)
    {
        $groupXml = $typeXml->addChild($group);

        if (is_array($attributes)) {
            foreach ($attributes as $attribute) {
                if (!empty($attribute['is_deleted'])) {
                    continue;
                }
                $attributeXml = $groupXml->addChild($attribute['code']);
                $attributeXml->addChild('label', $attribute['label']);
                if (isset($attribute['group'])) {
                    $attributeXml->addChild('group', $attribute['group']);
                }
                $attributeXml->addChild('type', $attribute['type']);
                $attributeXml->addChild('sort_order', $attribute['sort_order']);

                switch ($attribute['type']) {
                    case 'select':
                        $this->addSelectOptions($attribute, $attributeXml);
                        break;
                    case 'date':
                        $this->addDateOptions($attribute, $attributeXml);
                        break;
                    case 'country':
                        $this->addCountryOptions($attribute, $attributeXml);
                        break;
                    default:
                        break;
                }
                $this->addFrontendParams($attribute, $attributeXml);
            }
        }
    }

    /**
     * Add select type options to attribute node
     *
     * @param array $attribute
     * @param Magento_Simplexml_Element $itemXml
     */
    public function addSelectOptions($attribute, $itemXml)
    {
        if (isset($attribute['options']) && is_array($attribute['options'])) {
            $optionXml = $itemXml->addChild('options');
            foreach ($attribute['options'] as $option) {
                if (!empty($option['is_deleted'])) {
                    continue;
                }
                $optionXml->addChild($option['code'], $option['label']);
            }
            if (isset($attribute['default'])) {
                $itemXml->addChild('default', $attribute['options'][$attribute['default']]['code']);
            }
        }
    }

    /**
     * Add date type options to attribute node
     *
     * @param array $attribute
     * @param Magento_Simplexml_Element $itemXml
     */
    public function addDateOptions($attribute, $itemXml)
    {
        $dateFormat = (isset($attribute['date_format'])) ? $attribute['date_format'] : '';
        $itemXml->addChild('date_format', $dateFormat);
    }

    /**
     * Add region type options to attribute node
     *
     * @param array $attribute
     * @param Magento_Simplexml_Element $itemXml
     */
    public function addCountryOptions($attribute, $itemXml)
    {
        $regionCountry = (isset($attribute['show_region'])) ? $attribute['show_region'] : '';
        $itemXml->addChild('show_region', $regionCountry);
    }

    /**
     * Add frontend params to attribute node
     *
     * @param array $attribute
     * @param Magento_Simplexml_Element $itemXml
     */
    public function addFrontendParams($attribute, $itemXml)
    {
        if (isset($attribute['frontend']) && is_array($attribute['frontend'])) {
            $paramXml = $itemXml->addChild('frontend');
            foreach ($attribute['frontend'] as $param => $value) {
                $paramXml->addChild($param, $value);
            }
        }
    }

    /**
     * Convert attributes xml to array
     *
     * @param string $xmlString
     * @return array
     */
    public function processXml($xmlString = '')
    {
        if ($xmlString) {
            $xmlObj = new Magento_Simplexml_Element($xmlString);
            $attributes = $xmlObj->asArray();
            if (isset($attributes[self::XML_PROTOTYPE_NODE])) {
                return $attributes[self::XML_PROTOTYPE_NODE];
            }
        }
        return array();
    }
}
