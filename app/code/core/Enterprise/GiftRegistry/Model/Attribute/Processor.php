<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry custom attribute processor model
 */
class Enterprise_GiftRegistry_Model_Attribute_Processor extends Enterprise_Enterprise_Model_Core_Abstract
{
    /**
     * Render customer xml
     *
     * @param Enterprise_GiftRegistry_Model_Type $type
     * @return string
     */
    public function processData($type)
    {
        if ($attributes = $type->getAttributes()) {
            $xmlObj = new Varien_Simplexml_Element('<custom></custom>');

            foreach ($attributes as $attribute) {
                if (!empty($attribute['is_deleted'])) {
                    continue;
                }
                $itemXml = $xmlObj->addChild($attribute['code']);
                $itemXml->addChild('label', $attribute['label']);
                $itemXml->addChild('group', $attribute['group']);
                $itemXml->addChild('type', $attribute['type']);
                $itemXml->addChild('sort_order', $attribute['sort_order']);

                switch ($attribute['type']) {
                    case 'select': $this->getSelectOptions($attribute, $itemXml); break;
                }
            }
            return $xmlObj->asNiceXml();
        }
    }

    /**
     * Render xml select options
     *
     * @param array $attribute
     * @param Varien_Simplexml_Element $itemXml
     */
    public function getSelectOptions($attribute, $itemXml)
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
     * Render customer xml
     *
     * @return array
     */
    public function processXml($xmlString = '')
    {
        if ($xmlString) {
            $xmlObj = new Varien_Simplexml_Element($xmlString);
            return $xmlObj->asArray();
        }
    }
}
