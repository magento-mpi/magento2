<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Usa_Model_Shipping_Carrier_Fedex_Source_Generic implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Usa_Model_Shipping_Carrier_Fedex
     */
    protected $_shippingFedex;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * @param Magento_Usa_Model_Shipping_Carrier_Fedex $shippingFedex
     */
    public function __construct(Magento_Usa_Model_Shipping_Carrier_Fedex $shippingFedex)
    {
        $this->_shippingFedex = $shippingFedex;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingFedex->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => $title);
        }
        return $arr;
    }
}
