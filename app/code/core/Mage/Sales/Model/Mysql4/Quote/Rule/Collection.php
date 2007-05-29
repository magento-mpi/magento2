<?php
/**
 * Customers collection
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Mysql4_Price_Rule_Collection extends Varien_Data_Collection_Db
{
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
        
        $ruleTable = Mage::registry('resources')->getTableName('sales_resource', 'price_rule');
        $this->_sqlSelect->from($ruleTable);
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', 'price_rule'));
    }
}