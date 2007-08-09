<?php

class Mage_CatalogRule_Model_Rule_Action_Collection extends Mage_Rule_Model_Action_Collection
{
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(
            array('value'=>'catalogrule/rule_action_product', 'label'=>'Update the product')
        ));
        return $actions;
    }
}