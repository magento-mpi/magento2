<?php

class Mage_Sales_Model_Quote_Rule_Action_Collection extends Varien_Object_Collection
{
    /**
     * Returns array containing actions in the collection
     * 
     * Output example:
     * array(
     *   {action::toArray},
     *   {action::toArray}
     * )
     * 
     * @return array
     */
    public function toArray()
    {
        $out = array();
        
        foreach ($this->getItems() as $item) {
            $out[] = $item->toArray();
        }
        
        return $out;
    }
    
    public function loadArray($arr)
    {
        $salesConfig = Mage::getSingleton('sales', 'config');
        
        foreach ($arr as $componentArr) {
            $component = $salesConfig->getQuoteRuleConditionInstance($componentArr['action']);
            $component->loadArray($componentArr);
            $this->addItem($component);
        }
        return $this;
    }
}