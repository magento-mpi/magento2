<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fedex dropoff source implementation
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Model_Shipping_Carrier_Fedex_Source_Dropoff
    implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Fedex');
        $arr = array();
        foreach ($fedex->getCode('dropoff') as $k => $v) {
            $arr[] = array('value' => $k, 'label' => $v);
        }
        return $arr;
    }
}
