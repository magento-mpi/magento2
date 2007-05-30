<?php

abstract class Mage_Sales_Model_Quote_Rule_Action_Abstract extends Varien_Object implements Mage_Sales_Model_Quote_Rule_Action_Interface
{
    public function loadFromArray($arr)
    {
        $this->addData(array(
            'type'=>$arr['action'],
            'attribute'=>$arr['attribute'],
            'operator'=>$arr['operator'],
            'value'=>$arr['value'],
        ));
        return $this;
    }
}