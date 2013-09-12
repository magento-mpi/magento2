<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleCheckout\Model;

class Api extends \Magento\Object
{
    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    protected function _getApi($area)
    {
        $api = \Mage::getModel('Magento\GoogleCheckout\Model\Api\Xml\\' . uc_words($area))->setStoreId($this->getStoreId());
        $api->setApi($this);
        return $api;
    }

// CHECKOUT
    public function checkout(\Magento\Sales\Model\Quote $quote)
    {
        $api = $this->_getApi('checkout')
            ->setQuote($quote)
            ->checkout();
        return $api;
    }

// FINANCIAL COMMANDS
    public function authorize($gOrderId)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->authorize();
        return $api;
    }

    public function charge($gOrderId, $amount)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->charge($amount);
        return $api;
    }

    public function refund($gOrderId, $amount, $reason, $comment = '')
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->refund($amount, $reason, $comment);
        return $api;
    }

    public function cancel($gOrderId, $reason, $comment = '')
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->cancel($reason, $comment);
        return $api;
    }

// FULFILLMENT COMMANDS (ORDER BASED)

    public function process($gOrderId)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->process();
        return $api;
    }

    public function deliver($gOrderId, $carrier, $trackingNo, $sendMail = true)
    {
        $this->setCarriers(array('dhl' => 'DHL', 'fedex' => 'FedEx', 'ups' => 'UPS', 'usps' => 'USPS'));
        \Mage::dispatchEvent('googlecheckout_api_deliver_carriers_array', array('api' => $this));
        $gCarriers = $this->getCarriers();
        $carrier = strtolower($carrier);
        $carrier = isset($gCarriers[$carrier]) ? $gCarriers[$carrier] : 'Other';

        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->deliver($carrier, $trackingNo, $sendMail);
        return $api;
    }

    public function addTrackingData($gOrderId, $carrier, $trackingNo)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->addTrackingData($carrier, $trackingNo);
        return $api;
    }

// FULFILLMENT COMMANDS (ITEM BASED)

    public function shipItems($gOrderId, array $items)
    {
        $api = $this->_getApi('order')
            ->setGoogleOrderNumber($gOrderId)
            ->shipItems($items);
        return $api;
    }

    public function backorderItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function returnItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function cancelItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function resetItemsShippingInformation()
    {

    }

    public function addMerchantOrderNumber()
    {

    }

    public function sendBuyerMessage()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

// OTHER ORDER COMMANDS

    public function archiveOrder()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function unarchiveOrder()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

// WEB SERVICE SERVER PROCEDURES

    public function processCallback()
    {
        $api = $this->_getApi('callback')->process();
        return $api;
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    public function debugData($debugData)
    {
        if ($this->getDebugFlag()) {
            \Mage::getModel('Magento\Core\Model\Log\Adapter', array('fileName' => 'payment_googlecheckout.log'))
               ->setFilterDataKeys($this->_debugReplacePrivateDataKeys)
               ->log($debugData);
        }
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        if (!$this->hasData('debug_flag')) {
            $this->setData('debug_flag', \Mage::getStoreConfig('google/checkout/debug', $this->getStoreId()));
        }
        return $this->getData('debug_flag');
    }
}
