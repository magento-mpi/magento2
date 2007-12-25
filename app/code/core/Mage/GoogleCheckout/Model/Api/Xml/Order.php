<?php

class Mage_GoogleCheckout_Model_Api_Xml_Order extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    protected function _getApiUrl()
    {
        $url = $this->_getBaseApiUrl();
        $url .= 'request/Merchant/'.Mage::getStoreConfig('google/checkout/merchant_id');
        return $url;
    }
}