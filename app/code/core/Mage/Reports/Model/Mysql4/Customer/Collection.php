<?php
/**
 * Customers Report collection
 *
 * @package    Mage
 * @subpackage Reports
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */
 
class Mage_Reports_Model_Mysql4_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected function _construct()
    {
        parent::__construct();
    }
    
    public function addCartInfo()
    {
        foreach ($this->getItems() as $item)
        {        
            $quote = Mage::getResourceModel('sales/quote_collection')
                        ->loadByCustomerId($item->getId());
            
            if (is_object($quote))
            {
                $totals = $quote->getTotals();
                $item->setTotal($totals['subtotal']->getValue());
                $quote_items = Mage::getResourceModel('sales/quote_item_collection')->setQuoteFilter($quote->getId());
                $quote_items->load();
                $item->setItems($quote_items->count());
            } else {
                $item->setItems('0');
                $item->setTotal('0');
            }
            
        }
        return $this;
    }   
}
