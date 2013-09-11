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
namespace Magento\Usa\Model\Shipping\Carrier\Ups\Source;

class OriginShipment
{
    public function toOptionArray()
    {
        $orShipArr = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Ups')->getCode('originShipment');
        $returnArr = array();
        foreach ($orShipArr as $key => $val){
            $returnArr[] = array('value'=>$key,'label'=>$key);
        }
        return $returnArr;
    }
}
