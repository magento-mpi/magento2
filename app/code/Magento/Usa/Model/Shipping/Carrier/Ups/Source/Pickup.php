<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Ups_Source_Pickup
    extends Magento_Usa_Model_Shipping_Carrier_Ups_Source_Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'pickup';

    public function toOptionArray()
    {
        $ups = $this->_shippingUps->getCode($this->_code);
        $arr = array();
        foreach ($ups as $k => $v) {
            $arr[] = array('value'=>$k, 'label'=>__($v['label']));
        }
        return $arr;
    }
}
