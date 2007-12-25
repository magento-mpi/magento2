<?php

abstract class Mage_GoogleCheckout_Model_Api_Xml_Abstract extends Varien_Object
{
    protected function _getBaseApiUrl()
    {
        $url = 'https://';
        if (Mage::getStoreConfig('google/checkout/sandbox')) {
            $url .= 'sandbox.google.com/checkout/api/checkout/v2/';
        } else {
            $url .= 'checkout.google.com/api/checkout/v2/';
        }
        return $url;
    }

    abstract protected function _getApiUrl();

    public function _call($xml)
    {
        $auth = 'Basic '.base64_encode(
            Mage::getStoreConfig('google/checkout/merchant_id').':'.
            Mage::getStoreConfig('google/checkout/merchant_key')
        );

        $headers = array(
            'Authorization: '.$auth,
            'Content-Type: application/xml;charset=UTF-8',
            'Accept: application/xml;charset=UTF-8',
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n".$xml;
#echo '<xmp>'.$xml.'</xmp>';
        $http = new Varien_Http_Adapter_Curl();
        $http->write('POST', $this->_getApiUrl(), '1.1', $headers, $xml);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        $result = new SimpleXmlElement($response);
        if ($result->getName()=='error') {
            $this->setError((string)$result->{'error-message'});
            $this->setWarnings((array)$result->{'warning-messages'});
        } else {
            $this->unsError()->unsWarnings();
        }

        $this->setResult($result);

        return $result;
    }

    protected function _getEditCartUrl()
    {
        return Mage::getUrl('checkout/cart');
    }

    protected function _getContinueShoppingUrl()
    {
        return Mage::getUrl('');
    }

    protected function _getNotificationsUrl()
    {
        return Mage::getUrl('googlecheckout/api/notifications');
    }

    protected function _getCalculationsUrl()
    {
        return Mage::getUrl('googlecheckout/api/calculations');
    }

    protected function _getParameterizedUrl()
    {
        return Mage::getUrl('googlecheckout/api/parameterized');
    }
}