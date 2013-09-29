<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Config_Source_Tablerate implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Shipping_Model_Carrier_Tablerate
     */
    protected $_carrierTablerate;

    /**
     * @param Magento_Shipping_Model_Carrier_Tablerate $carrierTablerate
     */
    public function __construct(Magento_Shipping_Model_Carrier_Tablerate $carrierTablerate)
    {
        $this->_carrierTablerate = $carrierTablerate;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = array();
        foreach ($this->_carrierTablerate->getCode('condition_name') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
