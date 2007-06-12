<?php

/**
 * Quote rule action - add quote item
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Quote_Rule_Action_Quote_Item_Add extends Mage_Rule_Model_Action_Abstract
{
    /**
     * Perform some actions when assigning a rule to the object
     *
     * Create item anchor in the rule for optional further processing
     * 
     * @param Mage_Sales_Model_Quote_Rule $rule
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Item_Add
     */
    public function setRule(Mage_Sales_Model_Quote_Rule $rule)
    {
        $this->setData('rule', $rule);
        $number = $rule->getFoundQuoteItemNumber();
        $rule->setFoundQuoteItemNumber($number+1);
        $this->setItemNumber($number);
        return $this;
    }
    
    /**
     * Load attribute options
     *
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Item_Add
     */
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'product_id'=>'Product ID',
            'sku'=>'SKU',
            'qty'=>'Quantity',
            'brand'=>'Brand',
            'weight'=>'Weight',
            'price'=>'Price',
        ));
        return $this;
    }
    
    /**
     * Load array into the action object
     *
     * @param array $arr
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Item_Add
     */
    public function loadArray(array $arr)
    {
        $this->addData(array(
            'value'=>$arr['value'],
            'item_qty'=>$arr['item_qty'],
        ));
        return parent::loadArray($arr);
    }
    
    /**
     * Export the action to an array
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $arr = array(
            'type'=>'quote_item_add', 
            'value'=>$this->getValue(),
            'item_number'=>$this->getItemNumber(),
            'item_qty'=>$this->getItemQty(),
        );
        return $arr;
    }
    
    /**
     * Export the action as a string
     *
     * @param string $format
     * @return string
     */
    public function toString($format='')
    {
        $str = "Add ".$this->getItemQty()." product".($this->getItemQty()>1 ? 's' : '')." ".$this->getValueName()
            ." to the cart (# ".$this->getItemNumber().")";
        return $str;
    }
    
    /**
     * Update the quote using action's parameters
     *
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Item_Add
     */
    public function process()
    {
        
        return $this;
    }
}