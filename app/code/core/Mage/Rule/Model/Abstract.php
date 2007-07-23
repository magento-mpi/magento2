<?php

abstract class Mage_Rule_Model_Abstract extends Varien_Object
{
    public function __construct()
    {
        parent::__construct();
        $this->setStopProcessingRules(false);
        $this->resetConditions();
        $this->resetActions();
        $this->setForm(new Varien_Data_Form());
    }
    
    abstract public function getResource();
    
    public function resetConditions(Mage_Rule_Model_Condition_Interface $conditions=null)
    {
        if (is_null($conditions)) {
            $conditions = Mage::getModel('rule/condition_combine');
        }
        $conditions->setRule($this)->setId('1');
        $this->setConditions($conditions);

        return $this;
    }
    
    abstract public function getConditionInstance($type);
    
    public function resetActions(Mage_Rule_Model_Action_Interface $actions=null)
    {
        if (is_null($actions)) {
            $actions = Mage::getModel('rule/action_collection');
        }
        $actions->setRule($this);
        $this->setActions($actions);
        
        return $this;
    }

    abstract public function getActionInstance($type);
    
    public function asString($format='')
    {
        $str = "Name: ".$this->getName()."\n"
            ."Start at: ".$this->getStartAt()."\n"
            ."Expire at: ".$this->getExpireAt()."\n"
            ."Description: ".$this->getDescription()."\n\n"
            .$this->getConditions()->asStringRecursive()."\n\n"
            .$this->getActions()->asStringRecursive()."\n\n";
        return $str;
    }    
    
    public function asHtml($format='')
    {
        $str = "Name: ".$this->getName()."<br>"
            ."Start at: ".$this->getStartAt()."<br>"
            ."Expire at: ".$this->getExpireAt()."<br>"
            ."Description: ".$this->getDescription()."<br><br>"
            .$this->getConditions()->asHtmlRecursive()."<br><br>"
            .$this->getActions()->asHtmlRecursive()."<br><br>";
        return $str;
    }
    
    /**
     * Returns rule as an array for admin interface
     * 
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::asArray}
     *   'actions'=>{action_collection::asArray}
     * )
     * 
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = array(
            'name'=>$this->getName(),
            'start_at'=>$this->getStartAt(),
            'expire_at'=>$this->getExpireAt(),
            'description'=>$this->getDescription(),
            'conditions'=>$this->getConditions()->asArray(),
            'actions'=>$this->getActions()->asArray(),
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
        $conditions = serialize($this->getConditions()->asArray());
        $this->setConditionsSerialized($conditions);

        $actions = serialize($this->getActions()->asArray());
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