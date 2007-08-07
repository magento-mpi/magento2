<?php

class Mage_Sales_Model_Invoice_Shipment extends Mage_Core_Model_Abstract
{

    // TOFIX - what statuses should we have ?
    const STATUS_SENT = 1;
    const STATUS_SHIPPED = 2;
    const STATUS_RETURNED = 3;

    protected static $_statuses = null;

    protected $_invoice;

    function _construct()
    {
        $this->_init('sales/invoice_shipment');
    }

    public function setInvoice(Mage_Sales_Model_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    public function getInvoice()
    {
        return $this->_invoice;
    }


    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = array(
                // TOFIX - what statuses should we have ?
                self::STATUS_SENT => __('Sent'),
                self::STATUS_SHIPPED => __('Shipped'),
                self::STATUS_RETURNED => __('Returned'),
            );
        }
        return self::$_statuses;
    }

    public static function getStatusName($statusId)
    {
        if (is_null(self::$_statuses)) {
            self::getStatuses();
        }
        if (isset(self::$_statuses[$statusId])) {
            return self::$_statuses[$statusId];
        }
        return __('Unknown Status');
    }

}
