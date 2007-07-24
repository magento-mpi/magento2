<?php

/**
 * Quote rule action - update shipping address data
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Quote_Rule_Action_Quote_Address extends Mage_Rule_Model_Action_Abstract
{
    /**
     * Load attribute options
     *
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Address
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'subtotal'=>'Subtotal',
            'shipping_amount'=>'Shipping amount',
            'shipping_method'=>'Shipping method',
            'discount_amount'=>'Discount amount',
            'discount_percent'=>'Discount percent',
            'weight'=>'Weight',
        ));
        return $this;
    }
    
    public function getAddressNumSelectOptions()
    {
    	$opt = array();
    	for ($i=1, $l=$this->getRule()->getFoundQuoteAddressNumber(); $i<$l-1; $i++) {
    		$opt[] = array('value'=>$i, 'label'=>$i);
    	}
    	return $opt;
    }
    
    public function asHtml()
    {
    	$form = $this->getRule()->getForm();
    	$renderer = new Mage_Rule_Block_Editable();
    	
    	$attrEl = $form->addField('action:'.$this->getId().':attribute', 'select', array(
    		'values'=>$this->getAttributeSelectOptions(),
    		'value'=>$this->getAttribute(),
    		'value_name'=>$this->getAttributeName(),
    	))->setRenderer($renderer);
    	
    	$operEl = $form->addField('action:'.$this->getId().':operator', 'select', array(
    		'values'=>$this->getOperatorSelectOptions(),
    		'value'=>$this->getOperator(),
    		'value_name'=>$this->getOperatorName(),
    	))->setRenderer($renderer);
    	
    	$valueEl = $form->addField('action:'.$this->getId().':value', 'text', array(
    		'value'=>$this->getValue(),
    		'value_name'=>$this->getValueName(),
    	))->setRenderer($renderer);
    	
    	$addressNumEl = $form->addField('action:'.$this->getId().':address', 'select', array(
    		'values'=>$this->getAddressNumSelectOptions(),
    		'value'=>$this->getAddressNumber(),
    		'value_name'=>$this->getAddressNumber(),
    	))->setRenderer($renderer);
    	
    	$html = __("Update address # %s %s %s %s",
            $addressNumEl->getHtml(),
            $attrEl->getHtml(),
            $operEl->getHtml(),
            $valueEl->getHtml()
        );
    	
    	return $html;
    }
    
    /**
     * Export the action as a string
     *
     * @param string $format
     * @return string
     */
    public function asString($format='')
    {
        $str = "Update address # ".$this->getAddressNumber()." ".$this->getAttributeName()
            ." ".$this->getOperatorName()." ".$this->getValueName();
        return $str;
    }
    
    /**
     * Update quote using action's parameters
     *
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Address
     */
    public function process()
    {
        $addressNumber = $this->getAddressNumber();
        $entityId = $this->getRule()->getFoundQuoteAddresses($addressNumber);
        $address = $this->getObject()->getEntityById($entityId);
        
        switch ($this->getOperator()) {
            case '=':
                $value = $this->getValue();
                break;
                
            case '+=':
                $value = $address->getData($this->getAttribute())+$this->getValue();
        }
        $address->setData($this->getAttribute(), $value);
        
        return $this;
    }
}