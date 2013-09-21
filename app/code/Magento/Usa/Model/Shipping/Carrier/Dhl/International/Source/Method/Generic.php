<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Generic
{
    /**
     * @var Magento_Usa_Model_Shipping_Carrier_Dhl_International
     */
    protected $_shippingDhlInt;

    /**
     * @var string
     */
    protected $_code = '';

    /**
     * @param Magento_Usa_Model_Shipping_Carrier_Dhl_International $shippingDhlInt
     */
    public function __construct(Magento_Usa_Model_Shipping_Carrier_Dhl_International $shippingDhlInt)
    {
        $this->_shippingDhlInt = $shippingDhlInt;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingDhlInt->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => $title);
        }
        return $arr;
    }
}
