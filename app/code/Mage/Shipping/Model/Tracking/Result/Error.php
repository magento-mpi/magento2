<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Shipping_Model_Tracking_Result_Error extends Mage_Shipping_Model_Tracking_Result_Abstract
{
    public function getAllData()
    {
        return $this->_data;
    }

    public function getErrorMessage()
    {
        return  Mage::helper('Mage_Shipping_Helper_Data')->__('Tracking information is unavailable.');
    }
}
