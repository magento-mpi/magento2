<?php

class Mage_Sales_Model_Quote_Attribute extends Varien_Data_Object 
{
    protected $_entity = null;
    
    public function setEntity($entity)
    {
        $this->_entity = $entity;
    }
    
    public function getEntity()
    {
        return $this->_entity;
    }
    
    public function isEmpty()
    {
        extract($this->getData());
        $empty = (empty($attribute_int) || 0==$attribute_int) 
            && (empty($attribute_varchar) || ''===$attribute_varchar)
            && (empty($attribute_datetime) || ''===$attribute_datetime || '0000-00-00 00:00:00'===$attribute_datetime)
            && (empty($attribute_text) || ''===$attribute_text) 
            && (empty($attribute_decimal) || 0==$attribute_decimal);
        return $empty;
    }
    
    /**
     * This method will be used in child classes to collect total rows
     *
     * @return array
     */
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        return array();
    }

    
    public function asArray()
    {
        $arr = array();
        $arr['id'] = $this->getQuoteAttributeId();
        $arr['decimal'] = $this->getQuoteAttributeDecimal();
        $arr['text'] = $this->getQuoteAttributeText();
        $arr['int'] = $this->getQuoteAttributeInt();
        $arr['datetime'] = $this->getQuoteAttributeDatetime();
        $arr['varchar'] = $this->getQuoteAttributeVarchar();
        return $arr;
    }


}