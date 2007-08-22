<?php

class Mage_CatalogRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine 
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('catalogrule/rule_condition_combine');
    }
    
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'catalogrule/rule_condition_product', 'label'=>'Product attribute'),
            array('value'=>'catalogrule/rule_condition_combine', 'label'=>'Conditions combination'),
        ));
        return $conditions;
    }
        
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}