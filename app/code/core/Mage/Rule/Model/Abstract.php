<?php

abstract class Mage_Rule_Model_Abstract extends Varien_Object
{
    public function __construct()
    {
        parent::__construct();
        $this->setStopProcessingRules(false);
        $this->resetConditions();
        $this->resetActions();
    }
    
    abstract public function getResource();
    
    public function resetConditions(Mage_Rule_Model_Condition_Interface $conditions=null)
    {
        if (is_null($conditions)) {
            $conditions = Mage::getModel('core/rule_condition_combine');
        }
        $conditions->setRule($this)->setId('1');
        $this->setConditions($conditions);
        
        return $this;
    }
    
    abstract public function getConditionInstance($type);
    
    public function resetActions(Mage_Rule_Model_Action_Interface $actions=null)
    {
        if (is_null($actions)) {
            $actions = Mage::getModel('core/rule_action_collection');
        }
        $actions->setRule($this);
        $this->setActions($actions);
        
        return $this;
    }

    abstract public function getActionInstance($type);
    
    public function toString($format='')
    {
        $str = "Name: ".$this->getName()."\n"
            ."Start at: ".$this->getStartAt()."\n"
            ."Expire at: ".$this->getExpireAt()."\n"
            ."Description: ".$this->getDescription()."\n\n"
            .$this->getConditions()->toStringRecursive()."\n\n"
            .$this->getActions()->toStringRecursive()."\n\n";
        return $str;
    }
    
    /**
     * Returns rule as an array for admin interface
     * 
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     * 
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = array(
            'name'=>$this->getName(),
            'start_at'=>$this->getStartAt(),
            'expire_at'=>$this->getExpireAt(),
            'description'=>$this->getDescription(),
            'conditions'=>$this->getConditions()->toArray(),
            'actions'=>$this->getActions()->toArray(),
        );
        
        return $out;
    }
    
    public function process()
    {
        if ($this->validate()) {
            $this->getActions()->process();
        }
        return $this;
    }
    
    public function validate()
    {
        return $this->getConditions()->validate();
    }
    
    public function load($ruleId)
    {
        $data = $this->getResource()->load($ruleId);
        if (empty($data)) {
            return $this;
        }
        $this->addData($data);
        
        $conditionsArr = unserialize($this->getConditionsSerialized());
        $this->getConditions()->loadArray($conditionsArr);
        
        $actionsArr = unserialize($this->getActionsSerialized());
        $this->getActions()->loadArray($actionsArr);
        
        return $this;
    }
    
    public function save()
    {
        $conditions = serialize($this->getConditions()->toArray());
        $this->setConditionsSerialized($conditions);

        $actions = serialize($this->getActions()->toArray());
        $this->setActionsSerialized($actions);
        
        $this->getResource()->save($this);
        
        return $this;
    }
    
    public function delete($ruleId=null)
    {
        if (is_null($ruleId)) {
            $ruleId = $this->getId();
        }
        
        if ($ruleId) {
            $this->getResource()->delete($ruleId);
        }
        return $this;
    }

}