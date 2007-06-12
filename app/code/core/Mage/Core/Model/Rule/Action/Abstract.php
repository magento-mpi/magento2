<?php

/**
 * Quote rule action abstract
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Core_Model_Rule_Action_Abstract extends Varien_Object implements Mage_Core_Model_Rule_Action_Interface 
{
    public function __construct()
    {
        parent::__construct();
        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();
    }
    
    public function toArray(array $arrAttributes = array())
    {
        return array();
    }
    
    public function loadArray($arr)
    {
        $this->setType($arr['type']);
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
            '=' => 'to',
            '+=' => 'by',
        ));
        return $this;
    }
    
    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }
    
    public function loadValues()
    {
        return $this;
    }
    
    public function getValueName()
    {
        return $this->getValue();
    }
    
    public function toString($format='')
    {
        return "";
    }
    
    public function toStringRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->toString();
        return $str;
    }
}