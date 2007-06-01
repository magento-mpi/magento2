<?php

class Mage_Sales_Model_Quote_Rule_Action_Quote_Item_Add extends Mage_Sales_Model_Quote_Rule_Action_Abstract
{
    public function setRule(Mage_Sales_Model_Quote_Rule $rule)
    {
        $this->setData('rule', $rule);
        $number = $rule->getConditionItemNumber();
        $rule->setConditionItemNumber($number+1);
        $this->setItemNumber($number);
        return $this;
    }
    
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'product_id'=>'Product ID',
            'sku'=>'SKU',
            'qty'=>'Quantity',
            'brand'=>'Brand',
            'weight'=>'Weight',
            'price'=>'Price',
        ));
        return $this;
    }
    
    public function loadArray($arr)
    {
        $this->addData(array(
            'value'=>$arr['value'],
            'item_qty'=>$arr['item_qty'],
        ));
        return parent::loadArray($arr);
    }
    
    public function toArray(array $arrAttributes = array())
    {
        $arr = array(
            'type'=>'quote_item_add', 
            'value'=>$this->getValue(),
            'item_number'=>$this->getItemNumber(),
            'item_qty'=>$this->getItemQty(),
        );
        return $arr;
    }
    
    public function toString($format='')
    {
        $str = "Add ".$this->getItemQty()." product".($this->getItemQty()>1 ? 's' : '')." ".$this->getValueName()
            ." to the cart (# ".$this->getItemNumber().")";
        return $str;
    }
    
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        return $this;
    }
}