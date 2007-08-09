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
        
        foreach ($this->getAttributeOption() as $attr=>$dummy) { $this->setAttribute($attr); break; }
        foreach ($this->getOperatorOption() as $operator=>$dummy) { $this->setOperator($operator); break; }
        
        $this->setValue('...');
    }
    
    public function asArray(array $arrAttributes = array())
    {
        return array();
    }
    
    public function asXml()
    {
    	return '';
    }
    
    public function loadArray(array $arr)
    {
        $this->setType($arr['type']);
        return $this;
    }
    
    public function loadAttributeOptions()
    {
    	$this->setAttributeOption(array());
        return $this;
    }
    
    public function getAttributeSelectOptions()
    {
    	$opt = array();
    	foreach ($this->getAttributeOption() as $k=>$v) {
    		$opt[] = array('value'=>$k, 'label'=>$v);
    	}
    	return $opt;
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
    
    public function getOperatorSelectOptions()
    {
    	$opt = array();
    	foreach ($this->getOperatorOption() as $k=>$v) {
    		$opt[] = array('value'=>$k, 'label'=>$v);
    	}
    	return $opt;
    }
    
    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }
    
    public function loadValueOptions()
    {
    	$this->setValueOption(array());
        return $this;
    }
    
    public function getValueSelectOptions()
    {
    	$opt = array();
    	foreach ($this->getValueOption() as $k=>$v) {
    		$opt[] = array('value'=>$k, 'label'=>$v);
    	}
    	return $opt;
    }
    
    public function getValueName()
    {
        return $this->getValue();
    }
    
    public function getNewChildSelectOptions()
    {
        return array(
            array('value'=>'', 'label'=>'Please choose an action to add...'),
            array('value'=>'rule/action_stop', 'label'=>'Stop rules processing'),
        );
    }
    
    public function getNewChildName()
    {
        $options = $this->getNewChildSelectOptions();
        return $options[0]['label'];
    }
    
    public function asHtml()
    {
        return '';
    }
    
    public function asHtmlRecursive()
    {
        $str = $this->asHtml();
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