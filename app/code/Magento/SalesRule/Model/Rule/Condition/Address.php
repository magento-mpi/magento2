<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Rule_Condition_Address extends Magento_Rule_Model_Condition_Abstract
{
    /**
     * @var Magento_Directory_Model_Config_Source_Country
     */
    protected $_directoryCountry;

    /**
     * @var Magento_Directory_Model_Config_Source_Allregion
     */
    protected $_directoryAllregion;

    /**
     * @var Magento_Shipping_Model_Config_Source_Allmethods
     */
    protected $_shippingAllmethods;

    /**
     * @var Magento_Payment_Model_Config_Source_Allmethods
     */
    protected $_paymentAllmethods;

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Directory_Model_Config_Source_Country $directoryCountry
     * @param Magento_Directory_Model_Config_Source_Allregion $directoryAllregion
     * @param Magento_Shipping_Model_Config_Source_Allmethods $shippingAllmethods
     * @param Magento_Payment_Model_Config_Source_Allmethods $paymentAllmethods
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Directory_Model_Config_Source_Country $directoryCountry,
        Magento_Directory_Model_Config_Source_Allregion $directoryAllregion,
        Magento_Shipping_Model_Config_Source_Allmethods $shippingAllmethods,
        Magento_Payment_Model_Config_Source_Allmethods $paymentAllmethods,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_directoryCountry = $directoryCountry;
        $this->_directoryAllregion = $directoryAllregion;
        $this->_shippingAllmethods = $shippingAllmethods;
        $this->_paymentAllmethods = $paymentAllmethods;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = array(
            'base_subtotal' => __('Subtotal'),
            'total_qty' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'payment_method' => __('Payment Method'),
            'shipping_method' => __('Shipping Method'),
            'postcode' => __('Shipping Postcode'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
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

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method': case 'payment_method': case 'country_id': case 'region_id':
                return 'select';
        }
        return 'text';
    }

    /**
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = $this->_directoryCountry->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_directoryAllregion->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = $this->_shippingAllmethods->toOptionArray();
                    break;

                case 'payment_method':
                    $options = $this->_paymentAllmethods->toOptionArray();
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
        if (!$address instanceof Magento_Sales_Model_Quote_Address) {
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
