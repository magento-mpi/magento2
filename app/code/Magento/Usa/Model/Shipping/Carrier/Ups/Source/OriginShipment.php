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
class Magento_Usa_Model_Shipping_Carrier_Ups_Source_OriginShipment
    extends Magento_Usa_Model_Shipping_Carrier_Ups_Source_Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'originShipment';

    public function toOptionArray()
    {
        $orShipArr = $this->_shippingUps->getCode($this->_code);
        $returnArr = array();
        foreach ($orShipArr as $key => $val){
            $returnArr[] = array('value' => $key,'label' => $key);
        }
        return $returnArr;
    }
}
