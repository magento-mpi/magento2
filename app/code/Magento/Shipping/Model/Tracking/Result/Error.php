<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Tracking_Result_Error extends Magento_Shipping_Model_Tracking_Result_Abstract
{
    public function getAllData()
    {
        return $this->_data;
    }

    public function getErrorMessage()
    {
        return  __('Tracking information is unavailable.');
    }
}
