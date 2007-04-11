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