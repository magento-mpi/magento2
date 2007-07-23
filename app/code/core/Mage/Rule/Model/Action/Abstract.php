<?php

/**
 * Quote rule action abstract
 *
 * @package    Mage
 * @subpackage Core
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Rule_Model_Action_Abstract extends Varien_Object implements Mage_Rule_Model_Action_Interface 
{
    public function __construct()
    {
        parent::__construct();
        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();
    }
    
    public function asArray(array $arrAttributes = array())
    {
        return array();
    }
    
    public function loadArray($arr)
    {
        $this->setType($arr['type']);
        return $this;
    }
    
    public function loadAttributeOptions()
    {
        return $this;
    }
    
    public function getAttributeName()
    {
        return $this->getAttributeOption($this->getAttribute());
    }
    
    public function loadOperatorOptions()
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
    
    public function loadValueOptions()
    {
        return $this;
    }
    
    public function getValueName()
    {
        return $this->getValue();
    }
    
    public function asHtml($format='')
    {
        return "";
    }
    
    public function asHtmlRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->asHtml();
        return $str;
    }    
    public function asString($format='')
    {
        return "";
    }
    
    public function asStringRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->asString();
        return $str;
    }
    
    public function process()
    {
        return $this;
    }
}