<?php

class Mage_Sales_Model_Invoice extends Mage_Core_Model_Abstract
{

    const STATUS_OPEN = 1;
    const STATUS_PAYED = 2;
    const STATUS_CANCELED = 3;

    protected static $_statuses = null;

    protected function _construct()
    {
        $this->_init('sales/invoice');
    }

    public function createFromOrder(Mage_Sales_Model_Order $order)
    {
        return $this;
    }

    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = array(
                self::STATUS_OPEN => __('Pending'),
                self::STATUS_PAYED => __('Payed'),
                self::STATUS_CANCELED => __('Canceled'),
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
