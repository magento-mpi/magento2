<?php

class Mage_GoogleCheckout_Model_Api_Xml_Order extends Mage_GoogleCheckout_Model_Api_Xml_Abstract
{
    protected function _getApiUrl()
    {
        $url = $this->_getBaseApiUrl();
        $url .= 'request/Merchant/'.Mage::getStoreConfig('google/checkout/merchant_id');
        return $url;
    }

// FINANCIAL

    public function authorize($amount)
    {
        $GRequest = $this->getGRequest();

        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <authorize-order xmlns=\"".$GRequest->schema_url.
            "\" google-order-number=\"". $this->getGoogleOrderNumber() . "\"/>";

        $response = $GRequest->SendReq($GRequest->request_url,
                   $GRequest->GetAuthenticationHeaders(), $postargs);
        return $this->_processResponse($response);
    }

    public function charge($amount)
    {
        $response = $this->getGRequest()
            ->SendChargeOrder($this->getGoogleOrderNumber(), $amount);
        return $this->_processResponse($response);
    }

    public function refund($amount, $reason, $comment='')
    {
        $response = $this->getGRequest()
            ->SendRefundOrder($this->getGoogleOrderNumber(), $amount, $reason, $comment);
        return $this->_processResponse($response);
    }

    public function cancel($reason, $comment='')
    {
        $response = $this->getGRequest()
            ->SendCancelOrder($this->getGoogleOrderNumber(), $reason, $comment);
        return $this->_processResponse($response);
    }

// FULFILLMENT

    public function process()
    {
        $response = $this->getGRequest()
            ->SendProcessOrder($this->getGoogleOrderNumber());
        return $this->_processResponse($response);
    }

    public function deliver($carrier, $trackingNo, $sendMail=true)
    {
        $response = $this->getGRequest()
            ->SendDeliverOrder($this->getGoogleOrderNumber(), $carrier, $trackingNo, $sendMail?'true':'false');
        return $this->_processResponse($response);
    }

    public function addTrackingData($carrier, $trackingNo)
    {
        $response = $this->getGRequest()
            ->SendTrackingData($this->getGoogleOrderNumber(), $carrier, $trackingNo);
        return $this->_processResponse($response);
    }

    public function shipItems($items, $sendMail=true)
    {
        $response = $this->getGRequest()
            ->SendShipItems($this->getGoogleOrderNumber(), $items, $sendMail?'true':'false');
        return $this->_processResponse($response);
    }

    public function backorderItems($items, $sendMail=true)
    {
        $response = $this->getGRequest()
            ->SendBackorderItems($this->getGoogleOrderNumber(), $items, $sendMail?'true':'false');
        return $this->_processResponse($response);
    }

    public function cancelItems($items, $reason, $comment='', $sendMail=true)
    {
        $response = $this->getGRequest()
            ->SendCancelItems($this->getGoogleOrderNumber(), $items, $reason, $comment, $sendMail?'true':'false');
        return $this->_processResponse($response);
    }

    public function returnItems($items, $sendMail=true)
    {
        $response = $this->getGRequest()
            ->SendReturnItems($this->getGoogleOrderNumber(), $items, $sendMail?'true':'false');
        return $this->_processResponse($response);
    }

    public function resetItems($items, $sendMail=true)
    {
        $response = $this->getGRequest()
            ->SendRResetItemsShippingInformation($this->getGoogleOrderNumber(), $items, $sendMail?'true':'false');
        return $this->_processResponse($response);
    }

// MISC

    public function archive()
    {
        $response = $this->getGRequest()
            ->SendArchiveOrder($this->getGoogleOrderNumber());
        return $this->_processResponse($response);
    }

    public function unarchive()
    {
        $response = $this->getGRequest()
            ->SendUnarchiveOrder($this->getGoogleOrderNumber());
        return $this->_processResponse($response);
    }

    public function addOrderNumber($merchantOrder)
    {
        $response = $this->getGRequest()
            ->SendMerchantOrderNumber($this->getGoogleOrderNumber(), $merchantOrder);
        return $this->_processResponse($response);
    }


    public function addBuyerMessage($message, $sendMail=true)
    {
        $response = $this->getGRequest()
            ->SendBuyerMessage($this->getGoogleOrderNumber(), $message, $sendMail?'true':'false');
        return $this->_processResponse($response);
    }
}