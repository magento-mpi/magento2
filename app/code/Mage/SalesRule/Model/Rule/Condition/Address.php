<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_SalesRule_Model_Rule_Condition_Address extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'base_subtotal' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Subtotal'),
            'total_qty' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Total Items Quantity'),
            'weight' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Total Weight'),
            'payment_method' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Payment Method'),
            'shipping_method' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Shipping Method'),
            'postcode' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Shipping Postcode'),
            'region' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Shipping Region'),
            'region_id' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Shipping State/Province'),
            'country_id' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Shipping Country'),
        );

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
            case 'base_subtotal': case 'weight': case 'total_qty':
                return 'numeric';

            case 'shipping_method': case 'payment_method': case 'country_id': case 'region_id':
                return 'select';
        }
        return 'string';
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method': case 'payment_method': case 'country_id': case 'region_id':
                return 'select';
        }
        return 'text';
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = Mage::getModel('Mage_Directory_Model_Config_Source_Country')
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = Mage::getModel('Mage_Directory_Model_Config_Source_Allregion')
                        ->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = Mage::getModel('Mage_Shipping_Model_Config_Source_Allmethods')
                        ->toOptionArray();
                    break;

                case 'payment_method':
                    $options = Mage::getModel('Mage_Payment_Model_Config_Source_Allmethods')
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Magento_Object $object
     * @return bool
     */
    public function validate(Magento_Object $object)
    {
        $address = $object;
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            }
            else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }

        if ('payment_method' == $this->getAttribute() && ! $address->hasPaymentMethod()) {
            $address->setPaymentMethod($object->getQuote()->getPayment()->getMethod());
        }

        return parent::validate($address);
    }
}
