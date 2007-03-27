<?php
/**
 * Customer mysql4 model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4 extends Mage_Core_Model_Db
{
    function __construct()
    {
        parent::__construct();

        $this->_read = $this->_getConnection('customer_read');
        $this->_write = $this->_getConnection('customer_write');
    }

}