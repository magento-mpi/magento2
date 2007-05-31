<?php

/**
 * Abstract class for quote rule condition
 *
 */
abstract class Mage_Sales_Model_Quote_Rule_Condition_Abstract extends Varien_Object
{
    public function toArray(array $arrAttributes = array())
    {
        $out = array(
            'type'=>$this->getType(),
            'attribute'=>$this->getAttribute(),
            'operator'=>$this->getOperator(),
            'value'=>$this->getValue(),
        );
        return $out;
    }
    
    public function loadArray($arr)
    {
        $this->addData(array(
            'type'=>$arr['type'],
            'attribute'=>$arr['attribute'],
            'operator'=>$arr['operator'],
            'value'=>$arr['value'],
        ));
        $this->loadAttributes();
        $this->loadOperators();
        $this->loadValues();
        return $this;
    }
    
    public function loadAttributes()
    {
        return $this;
    }
    
    public function getAttributeName()
    {
        return $this->getAttributeOption($this->getAttribute());
    }
    
    public function loadOperators()
    {
        $this->setOperatorOption(array(
            '='        => 'is',
            '!='       => 'is not',
            '>='       => 'equals or greater than',
            '<='       => 'equals or less than',
            '>'        => 'greater than',
            '<'        => 'less than',
            'like'     => 'contains',
            'not like' => 'does not contain',
            'in'       => 'one of',
            'not in'   => 'not one of',
        ));
        return $this;
    }
    
    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }
    
    public function loadValues()
    {
        $this->setValueOption(array(
            true => 'TRUE',
            false => 'FALSE',
        ));
        return $this;
    }
    
    public function getValueName()
    {
        $value = $this->getValue();
        if (is_string($value)) {
            return "'$value'";
        }
        if (is_bool($value)) {
            return $this->getValueOption($value);
        }
        return $value;
    }
    
    public function toString($format='')
    {
        $str = $this->getAttributeName().' '.$this->getOperatorName().' '.$this->getValueName();
        return $str;
    }
    
    public function toStringRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->toString();
        return $str;
    }
    
    /**
     * Validate quote against condition
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return boolean
     */
    public function validateQuote(Mage_Sales_Model_Quote $quote)
    {
        return true;
    }
}