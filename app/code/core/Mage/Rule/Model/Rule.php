<?php

class Mage_Rule_Model_Rule extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
    	$this->_init('rule/rule');
        $this->setStopProcessingRules(false);
        $this->resetConditions();
        $this->resetActions();
        $this->setForm(new Varien_Data_Form());
    }

    public function _resetConditions(Mage_Rule_Model_Condition_Interface $conditions=null)
    {
        if (is_null($conditions)) {
            $conditions = Mage::getModel('rule/condition_combine');
        }
        $conditions->setRule($this)->setId('1');
        $this->setConditions($conditions);

        return $this;
    }
    
    public function _resetActions(Mage_Rule_Model_Action_Interface $actions=null)
    {
        if (is_null($actions)) {
            $actions = Mage::getModel('rule/action_collection');
        }
        $actions->setRule($this)->setId('1');
        $this->setActions($actions);
        
        return $this;
    }

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
    
    public function asHtml()
    {
        $str = "Name: ".$this->getName()."<br>"
            ."Start at: ".$this->getStartAt()."<br>"
            ."Expire at: ".$this->getExpireAt()."<br>"
            ."Description: ".$this->getDescription().'<br>'
            .'<ul class="rule-conditions">'.$this->getConditions()->asHtmlRecursive().'</ul>'
            .'<ul class="rule-actions">'.$this->getActions()->asHtmlRecursive()."</ul>";
        return $str;
    }
    
    public function loadPost(array $rule)
    {
    	$arr = array();
    	foreach (array('conditions', 'actions') as $component) {
	    	foreach ($rule[$component] as $id=>$data) {
	    		$path = explode('.', $id);
	    		$node =& $arr;
	    		for ($i=0, $l=sizeof($path); $i<$l; $i++) {
	    			if (!isset($node[$component][$path[$i]])) {
	    				$node[$component][$path[$i]] = array();
	    			}
	    			$node =& $node[$component][$path[$i]];
	    		}
	    		foreach ($data as $k=>$v) {
	    			$node[$k] = $v;
	    		}
	    	}
    	}
echo '<pre>'.print_r($arr,1).'</pre>';
    	$this->getConditions()->loadArray($arr['conditions'][1]);
    	$this->getActions()->loadArray($arr['actions'][1]);
    	return $this;
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
    
    protected function _afterLoad()
    {
		$conditionsArr = unserialize($this->getConditionsSerialized());
        $this->getConditions()->loadArray($conditionsArr);
        
        $actionsArr = unserialize($this->getActionsSerialized());
        $this->getActions()->loadArray($actionsArr);
    }

    protected function _beforeSave()
    {
        $conditions = serialize($this->getConditions()->asArray());
        $this->setConditionsSerialized($conditions);

        $actions = serialize($this->getActions()->asArray());
        $this->setActionsSerialized($actions);
    }
}