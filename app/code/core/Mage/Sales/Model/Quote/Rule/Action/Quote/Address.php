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
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'postcode'=>'Zip code',
            'region_id'=>'Region/State',
            'country_id'=>'Country',
        ));
        return $this;
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