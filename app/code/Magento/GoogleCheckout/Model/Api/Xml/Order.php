<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleCheckout_Model_Api_Xml_Order extends Magento_GoogleCheckout_Model_Api_Xml_Abstract
{
    protected function _getApiUrl()
    {
        $url = $this->_getBaseApiUrl();
        $url .= 'request/Merchant/'.$this->_coreStoreConfig->getConfig('google/checkout/merchant_id', $this->getStoreId());
        return $url;
    }

    protected function _processGResponse($response)
    {
        if ($response[0]===200) {
            return true;
        } else {
            $xml = simplexml_load_string(html_entity_decode($response[1]));
            if (!$xml || !$xml->{'error-message'}) {
                return false;
            }
            Mage::throwException(__('Google Checkout: %1', (string)$xml->{'error-message'}));
        }
    }

// FINANCIAL

    public function authorize()
    {
        $GRequest = $this->getGRequest();

        $postargs = '<?xml version="1.0" encoding="UTF-8"?>
            <authorize-order xmlns="'
            . $GRequest->schema_url
            . '" google-order-number="'
            . $this->getGoogleOrderNumber()
            . '"/>';

        $response = $GRequest->SendReq($GRequest->request_url,
                   $GRequest->GetAuthenticationHeaders(), $postargs);
        return $this->_processGResponse($response);
    }

    public function charge($amount)
    {
        $response = $this->getGRequest()
            ->SendChargeOrder($this->getGoogleOrderNumber(), $amount);
        return $this->_processGResponse($response);
    }

    public function refund($amount, $reason, $comment = '')
    {
        $response = $this->getGRequest()
            ->SendRefundOrder($this->getGoogleOrderNumber(), $amount, $reason, $comment);
        return $this->_processGResponse($response);
    }

    public function cancel($reason, $comment = '')
    {
        $response = $this->getGRequest()
            ->SendCancelOrder($this->getGoogleOrderNumber(), $reason, $comment);
        return $this->_processGResponse($response);
    }

// FULFILLMENT

    public function process()
    {
        $response = $this->getGRequest()
            ->SendProcessOrder($this->getGoogleOrderNumber());
        return $this->_processGResponse($response);
    }

    public function deliver($carrier, $trackingNo, $sendMail = true)
    {
        $response = $this->getGRequest()
            ->SendDeliverOrder($this->getGoogleOrderNumber(), $carrier, $trackingNo, $sendMail ? 'true' : 'false');
        return $this->_processGResponse($response);
    }

    public function addTrackingData($carrier, $trackingNo)
    {
        $response = $this->getGRequest()
            ->SendTrackingData($this->getGoogleOrderNumber(), $carrier, $trackingNo);
        return $this->_processGResponse($response);
    }

    public function shipItems($items, $sendMail = true)
    {
        $googleShipItems = array();
        foreach ($items as $item) {
            $googleShipItems[] = new GoogleShipItem($item);
        }

        $response = $this->getGRequest()
            ->SendShipItems($this->getGoogleOrderNumber(), $googleShipItems, $sendMail ? 'true' : 'false');
        return $this->_processGResponse($response);
    }

    public function backorderItems($items, $sendMail = true)
    {
        $response = $this->getGRequest()
            ->SendBackorderItems($this->getGoogleOrderNumber(), $items, $sendMail ? 'true' : 'false');
        return $this->_processGResponse($response);
    }

    public function cancelItems($items, $reason, $comment = '', $sendMail = true)
    {
        $response = $this->getGRequest()
            ->SendCancelItems($this->getGoogleOrderNumber(), $items, $reason, $comment, $sendMail ? 'true' : 'false');
        return $this->_processGResponse($response);
    }

    public function returnItems($items, $sendMail = true)
    {
        $response = $this->getGRequest()
            ->SendReturnItems($this->getGoogleOrderNumber(), $items, $sendMail ? 'true' : 'false');
        return $this->_processGResponse($response);
    }

    public function resetItems($items, $sendMail = true)
    {
        $response = $this->getGRequest()
            ->SendRResetItemsShippingInformation($this->getGoogleOrderNumber(), $items, $sendMail ? 'true' : 'false');
        return $this->_processGResponse($response);
    }

// MISC

    public function archive()
    {
        $response = $this->getGRequest()
            ->SendArchiveOrder($this->getGoogleOrderNumber());
        return $this->_processGResponse($response);
    }

    public function unarchive()
    {
        $response = $this->getGRequest()
            ->SendUnarchiveOrder($this->getGoogleOrderNumber());
        return $this->_processGResponse($response);
    }

    public function addOrderNumber($merchantOrder)
    {
        $response = $this->getGRequest()
            ->SendMerchantOrderNumber($this->getGoogleOrderNumber(), $merchantOrder);
        return $this->_processGResponse($response);
    }


    public function addBuyerMessage($message, $sendMail = true)
    {
        $response = $this->getGRequest()
            ->SendBuyerMessage($this->getGoogleOrderNumber(), $message, $sendMail ? 'true' : 'false');
        return $this->_processGResponse($response);
    }
}
