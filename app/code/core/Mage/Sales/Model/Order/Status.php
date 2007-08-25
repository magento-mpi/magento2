<?php

class Mage_Sales_Model_Order_Status extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('sales/order_status');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        $label = '';
        if ($storeId = Mage::getSingleton('core/store')->getId()) {
            $label = Mage::getSingleton('core/store')->getConfig('sales/order_statuses/status_' . $this->getId());
        }
        if (! $label) {
            $label = $this->getData('frontend_label');
        }
        return $label;
    }

}
