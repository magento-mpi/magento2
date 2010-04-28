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
     * @return string
     */
    public function processData($attributes = null)
    {
        if (empty($attributes)) {
            return '';
        }

        $xmlObj = new Varien_Simplexml_Element('<custom></custom>');

        foreach ($attributes as $attribute) {
            $label = (isset($attribute['label_default'])) ? $attribute['label_default'] : $attribute['label'];
            $sortOrder = (isset($attribute['sort_order_default'])) ? $attribute['sort_order_default'] : $attribute['sort_order'];

            $itemXmlObj = $xmlObj->addChild($attribute['code']);
            $itemXmlObj->addChild('label', $label);
            $itemXmlObj->addChild('group', $attribute['group']);
            $itemXmlObj->addChild('type', $attribute['type']);
            $itemXmlObj->addChild('sort_order', $sortOrder);
        }
        return $xmlObj->asNiceXml();
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
