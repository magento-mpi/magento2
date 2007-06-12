<?php

class Mage_Core_Model_Rule_Action_Collection extends Mage_Core_Model_Rule_Action_Abstract
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
        $salesConfig = Mage::getSingleton('sales/config');
        
        foreach ($arr as $actArr) {
            $action = $this->getRule()->getActionInstance($actArr['type']);
            $action->loadArray($actArr);
            $this->addAction($action);
        }
        return $this;
    }
    
    public function addAction(Mage_Coer_Model_Rule_Action_Interface $action)
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
}