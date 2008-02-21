<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Rule_Condition_Quote extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'subtotal' => Mage::helper('salesrule')->__('Subtotal'),
            'weight' => Mage::helper('salesrule')->__('Total Weight'),
            'shipping_method' => Mage::helper('salesrule')->__('Shipping Method'),
            'payment_method' => Mage::helper('salesrule')->__('Payment Method'),
            'total_qty' => Mage::helper('salesrule')->__('Total Items Quantity'),
            'postcode' => Mage::helper('salesrule')->__('Shipping Postcode'),
            'region' => Mage::helper('salesrule')->__('Shipping State/Region'),
            'country_id' => Mage::helper('salesrule')->__('Shipping Country'),
        );

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'subtotal': case 'weight': case 'total_qty':
                return 'numeric';

            case 'shipping_method': case 'payment_method': case 'country_id':
                return 'select';
        }
        return 'string';
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method': case 'payment_method': case 'country_id':
                return 'select';
        }
        return 'text';
    }

    public function getValueSelectOptions()
    {
        switch ($this->getAttribute()) {
            case 'country_id':
                return Mage::getModel('adminhtml/system_config_source_country')
                    ->toOptionArray();

            case 'shipping_method':
                return Mage::getModel('adminhtml/system_config_source_shipping_allowedmethods')
                    ->toOptionArray();

            case 'payment_method':
                return Mage::getModel('adminhtml/system_config_source_payment_allowedmethods')
                    ->toOptionArray();
        }
        return array();
    }
}
