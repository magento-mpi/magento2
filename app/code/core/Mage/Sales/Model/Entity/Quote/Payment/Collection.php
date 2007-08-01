<?php

/**
 * Quote addresses collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Moshe Gurvich <moshe@varien.com>
 */

class Mage_Sales_Model_Entity_Quote_Payment_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getSingleton('sales_entity/quote_payment'));
        $this->setObject('sales/quote_payment');
    }
    
    public function setQuoteFilter($quoteId)
    {
        $this->addAttributeToFilter('parent_id', $quoteId);
        return $this;
    }
}