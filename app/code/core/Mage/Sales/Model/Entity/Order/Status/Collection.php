<?php
/**
 * Order status collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_Model_Entity_Order_Status_Collection extends Varien_Data_Collection_Db
{

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('sales_read'));
        $this->_sqlSelect->from(Mage::getSingleton('core/resource')->getTableName('sales/order_status'))->order('order_status_id');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('order_status_id', 'frontend_label');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('order_status_id', 'frontend_label');
    }

}
