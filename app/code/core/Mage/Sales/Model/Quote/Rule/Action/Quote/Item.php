<?php

/**
 * Quote rule action - update quote item data
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Quote_Rule_Action_Quote_Item extends Mage_Rule_Model_Action_Abstract
{
    /**
     * Load attribute options
     *
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Item
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
     * Load an array as the action parameters
     *
     * @param array $arr
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Item
     */
    public function loadArray(array $arr)
    {
        $this->addData(array(
            'attribute'=>$arr['attribute'],
            'operator'=>$arr['operator'],
            'value'=>$arr['value'],
            'item_number'=>$arr['item_number'],
            'item_qty'=>$arr['item_qty'],
        ));
        return parent::loadArray($arr);
    }
    
    /**
     * Export the action as an array
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $arr = array(
            'type'=>'quote_item', 
            'attribute'=>$this->getAttribute(),
            'operator'=>$this->getOperator(),
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
        /*
        $str = "Update item # ".$this->getItemNumber()." ".$this->getAttributeName()
            ." ".$this->getOperatorName()." ".$this->getValueName()
            ." for ".$this->getItemQty()." item".($this->getItemQty()>1 ? 's' : '');
        */
        // just playing around to see how it is possible to integrate languages...
        // even though rules toString shouldn't be used but in admin interface.
        // most probably it won't work because of different sentence build rules
        // really don't want to create different classes for languages
        // maybe named substitutions would help
        // looks like english is the simplest choice (for now)
        $str = __("Update item # %d %s %s %s for %d %s",
            $this->getItemNumber(),
            $this->getAttributeName(),
            $this->getOperatorName(),
            $this->getValueName(),
            $this->getItemQty(),
            $this->getItemQty()>1 ? __('items') : __('item')
        );
        return $str;
    }
    
    /**
     * Update quote using the action's parameters
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Sales_Model_Quote_Rule_Action_Quote_Item
     */
    public function process()
    {
        $itemNumber = $this->getItemNumber();
        $entityId = $this->getRule()->getFoundQuoteItems($itemNumber);
        $item = $this->getObject()->getEntityById($entityId);
        
        switch ($this->getOperator()) {
            case '=':
                $value = $this->getValue();
                break;
                
            case '+=':
                $value = $item->getData($this->getAttribute())+$this->getValue();
        }
        $item->setData($this->getAttribute(), $value);
        
        return $this;
    }
}