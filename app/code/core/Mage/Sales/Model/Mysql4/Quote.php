<?php

class Mage_Sales_Model_Mysql4_Quote extends Mage_Sales_Model_Mysql4_Document
{
    public function __construct($data=array())
    {
        parent::__construct();
        $this->setDocType('quote');
    }
    
    public function getQuoteIdsByCustomerId($customerId)
    {
        $result = $this->_read->fetchAssoc("select q.quote_id from ".$this->_documentTable." q inner join ".$this->_attributeTable."_int a on  a.quote_id=q.quote_id and a.entity_type='self' and a.attribute_code='customer_id' where a.attribute_value=?", $customerId);
        if (empty($result)) {
            return false;
        }
        return array_keys($result);
    }
}