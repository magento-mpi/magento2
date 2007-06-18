<?php
class Mage_Log_Model_Customer extends Varien_Object
{
    public function getResource()
    {
        return Mage::getModel('log_resource/customers_collection');
    }

    public function getOnlineList()
    {
        $data = $this->getResource()->load();
        $this->setData($data);
        return $this;
    }

    public function addIpData($data)
    {
        $ipData = array();
        $data->setIpData($ipData);
        return $this;
    }

    public function addCustomerData($data)
    {
        $customerId = $data->getCustomerId();
        if( intval($customerId) <= 0 ) {
            return $this;
        }
        $data->setCustomerData(Mage::getModel('customer/customer')->load($customerId)->getData());
        return $this;
    }

    public function addQuoteData($data)
    {
        $quoteId = $data->getQuoteId();
        if( intval($quoteId) <= 0 ) {
            return $this;
        }
        $data->setQuoteData(Mage::getModel('sales/quote')->load($quoteId));
        return $this;
    }
}
