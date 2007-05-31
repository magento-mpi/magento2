<?php

class Mage_Sales_Model_Quote_Rule_Action_Collection extends Mage_Sales_Model_Quote_Rule_Action_Abstract
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
    public function toArray(array $arrAttributes = array())
    {
        $out = array();
        
        foreach ($this->getActions() as $item) {
            $out[] = $item->toArray();
        }
        
        return $out;
    }
    
    public function loadArray($arr)
    {
        $salesConfig = Mage::getSingleton('sales', 'config');
        
        foreach ($arr as $actArr) {
            $action = $salesConfig->getQuoteRuleActionInstance($actArr['type']);
            $action->loadArray($actArr);
            $this->addAction($action);
        }
        return $this;
    }
    
    public function addAction(Mage_Sales_Model_Quote_Rule_Action_Abstract $action)
    {
        $actions = $this->getActions();
        
        $action->setRule($this->getRule());

        $actions[] = $action;
        if (!$action->getId()) {
            $action->setId($this->getId().'.'.sizeof($action));
        }
        
        $this->setActions($actions);
        return $this;
    }
    
    public function toString($format='')
    {
        $str = "Perform following actions";
        return $str;
    }
    
    public function toStringRecursive($level=0)
    {
        $str = $this->toString();
        foreach ($this->getActions() as $action) {
            $str .= "\n".$action->toStringRecursive($level+1);
        }
        return $str;
    }
    
    public function updateQuote(Mage_Sales_Model_Quote $quote)
    {
        return $this;
    }
}