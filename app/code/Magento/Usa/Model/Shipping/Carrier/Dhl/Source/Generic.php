<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Usa_Model_Shipping_Carrier_Dhl_Source_Generic
{
    /**
     * @var Magento_Usa_Model_Shipping_Carrier_Dhl
     */
    protected $_shippingDhl;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * @param Magento_Usa_Model_Shipping_Carrier_Dhl $shippingDhl
     */
    public function __construct(Magento_Usa_Model_Shipping_Carrier_Dhl $shippingDhl)
    {
        $this->_shippingDhl = $shippingDhl;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingDhl->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => $title);
        }
        return $arr;
    }
}
