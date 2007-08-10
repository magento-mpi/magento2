<?php
/**
 * Quotes collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Moshe Gurvich <moshe@varien.com>
 */

class Mage_Sales_Model_Entity_Quote_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getResourceSingleton('sales/quote'));
        $this->setObject('sales/quote');
    }
    
    public function loadByCustomerId($customerId)
    {
        $this->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter('is_active', 1)
            ->setOrder('updated_at', 'desc')
            ->setPage(1,1)
            ->load();
        
        if (!$this->count()) {
            return false;
        }
        foreach ($this as $quote) {
            return $quote;
        }
    }
    
}