<?php

class Mage_Sales_Model_Quote_Rule_Action_Quote extends Mage_Rule_Model_Action_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'coupon_code'=>'Coupon code',
            'subtotal'=>'Subtotal',
            'currency_code'=>'Currency',
            'shipping_amount'=>'Shipping amount',
            'shipping_method'=>'Shipping method',
            'discount_amount'=>'Discount amount',
            'discount_percent'=>'Discount percent',
            'weight'=>'Weight',
        ));
        return $this;
    }
    
    public function loadArray($arr)
    {
        $this->addData(array(
            'attribute'=>$arr['attribute'],
            'operator'=>$arr['operator'],
            'value'=>$arr['value'],
        ));
        return parent::loadArray($arr);
    }
    
    public function asArray(array $arrAttributes = array())
    {
        $arr = array(
            'type'=>'quote', 
            'attribute'=>$this->getAttribute(),
            'operator'=>$this->getOperator(),
            'value'=>$this->getValue(),
        );
        return $arr;
    }
    
    public function asString($format='')
    {
        $str = "Update cart ".$this->getAttributeName()
            ." ".$this->getOperatorName()." ".$this->getValueName();
        return $str;
    }
    
    public function process()
    {
        switch ($this->getOperator()) {
            case '=':
                $value = $this->getValue();
                break;
                
            case '+=':
                $value = $this->getObject()->getData($this->getAttribute())+$this->getValue();
        }
        $quote->setData($this->getAttribute(), $value);
        
        return $this;
    }
}