<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * Usa Ups type action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_OriginShipment
{
    public function toOptionArray()
    {
        $orShipArr = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Ups')->getCode('originShipment');
        $returnArr = array();
        foreach ($orShipArr as $key => $val){
            $returnArr[] = array('value'=>$key,'label'=>$key);
        }
        return $returnArr;
    }
}
