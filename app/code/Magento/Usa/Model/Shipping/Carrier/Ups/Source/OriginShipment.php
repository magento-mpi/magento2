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
 *
 * Usa Ups type action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Model_Shipping_Carrier_Ups_Source_OriginShipment implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $orShipArr = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Ups')->getCode('originShipment');
        $returnArr = array();
        foreach ($orShipArr as $key => $val){
            $returnArr[] = array('value'=>$key,'label'=>$key);
        }
        return $returnArr;
    }
}
