<?php

/**
 * Abstract class for quote rule condition
 *
 */
abstract class Mage_Rule_Model_Condition_Abstract 
	extends Varien_Object 
	implements Mage_Rule_Model_Condition_Interface 
{
    public function __construct()
    {
        parent::__construct();
        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();
    }
    
    public function asArray(array $arrAttributes = array())
    {
        $out = array(
            'type'=>$this->getType(),
            'attribute'=>$this->getAttribute(),
            'operator'=>$this->getOperator(),
            'value'=>$this->getValue(),
        );
        return $out;
    }
    
    public function asXml()
    {
        extract($this->toArray());
        $xml = "<type>".$this->getType()."</type>"
            ."<attribute>".$this->getAttribute()."</attribute>"
            ."<operator>".$this->getOperator()."</operator>"
            ."<value>".$this->getValue()."</value>";
        return $xml;
    }
    
    public function loadArray($arr)
    {
        $this->addData(array(
            'type'=>$arr['type'],
            'attribute'=>$arr['attribute'],
            'operator'=>$arr['operator'],
            'value'=>$arr['value'],
        ));
        $this->loadAttributeOptions();
        $this->loadOperatorOptions();
        $this->loadValueOptions();
        return $this;
    }

    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $arr = (array)$xml;
        $this->loadArray($arr);
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
        $this->setValueOption(array(
            true  => 'TRUE',
            false => 'FALSE',
        ));
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
        $value = $this->getValue();
        if (is_string($value)) {
            return "'$value'";
        }
        if (is_bool($value)) {
            return $this->getValueOption($value);
        }
        return $value;
    }
    
    public function asHtml()
    {
    	$form = $this->getRule()->getForm();
    	$renderer = new Mage_Rule_Block_Editable();
    	
    	$attrEl = $form->addField('cond:'.$this->getId().':attribute', 'select', array(
    		'values'=>$this->getAttributeSelectOptions(),
    		'value'=>$this->getAttribute(),
    		'value_name'=>$this->getAttributeName(),
    	))->setRenderer($renderer);
    	
    	$operEl = $form->addField('cond:'.$this->getId().':operator', 'select', array(
    		'values'=>$this->getOperatorSelectOptions(),
    		'value'=>$this->getOperator(),
    		'value_name'=>$this->getOperatorName(),
    	))->setRenderer($renderer);
    	
    	$valueEl = $form->addField('cond:'.$this->getId().':value', 'text', array(
    		'value'=>$this->getValue(),
    		'value_name'=>$this->getValueName(),
    	))->setRenderer($renderer);
    	
    	$html = $attrEl->getHtml().' '.$operEl->getHtml().' '.$valueEl->getHtml();
    	return $html;
    }
    
    public function asHtmlRecursive($level=0)
    {
        $str = str_pad('', $level*3*6, '&nbsp;', STR_PAD_LEFT).$this->asHtml();
        return $str;
    }
    
    public function asString($format='')
    {
        $str = $this->getAttributeName().' '.$this->getOperatorName().' '.$this->getValueName();
        return $str;
    }
    
    public function asStringRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->asString();
        return $str;
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

    public function validate()
    {
        return $this->validateAttribute($this->getObject()->getData($this->getAttribute()));
    }
}