<?php

class Mage_SalesRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine 
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_combine');
    }
    
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'salesrule/rule_condition_product', 'label'=>'Product attribute'),
            array('value'=>'salesrule/rule_condition_combine', 'label'=>'Conditions combination'),
        ));
        return $conditions;
    }
}