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
            '=='  => 'is',
            '!='  => 'is not',
            '>='  => 'equals or greater than',
            '<='  => 'equals or less than',
            '>'   => 'greater than',
            '<'   => 'less than',
            '{}'  => 'contains',
            '!{}' => 'does not contain',
            '()'  => 'is one of',
            '!()' => 'is not one of',
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
            true  => 'TRUE',
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
        return false;
    }
    
    public function validateAttribute($validatedValue)
    {
        // $validatedValue suppose to be simple alphanumeric value
        if (is_array($validatedValue) || is_object($validatedValue)) {
            return false;
        }
        
        $op = $this->getOperator();
        
        // if operator requires array and it is not, or on opposite, return false
        if ((($op=='()' || $op=='!()') && !is_array($this->getValue()))
            || (!($op=='()' || $op=='!()') && is_array($this->getValue()))) {
            return false;
        }
        
        $result = false;
        
        switch ($op) {
            case '==': case '!=':
                $result = $this->getValue()==$validatedValue;
                break;

            case '<=': case '>':
                $result = $this->getValue()<=$validatedValue;
                break;
                
            case '>=': case '<':
                $result = $this->getValue()>=$validatedValue;
                break;
                
            case '{}': case '!{}':
                $result = strpos((string)$validatedValue, (string)$this->getValue())!==false;
                break;

            case '()': case '!()':
                $result = in_array($validatedValue, (array)$this->getValue());
                break;
        }
        
        if ('!='==$op || '>'==$op || '<'==$op || '!{}'==$op || '!()'==$op) {
            $result = !$result;
        }
        
        return $result;
    }
}