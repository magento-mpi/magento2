<?php

/**
 * Quote rule action - update shipping address data
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Quote_Rule_Action_Quote_Address extends Mage_Sales_Model_Quote_Rule_Action_Abstract
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
    public function toString($format='')
    {
        $str = "Update address # ".$this->getAddressNumber()." ".$this->getAttributeName()
            ." ".$this->getOperatorName()." ".$this->getValueName();
        return $str;
    }
    
    /**
     * Update quote using action's parameters
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Address
     */
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        return $this;
    }
}