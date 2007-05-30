<?php

interface Mage_Sales_Model_Quote_Rule_Action_Interface
{
    /**
     * Action data as array to be saved or shown in admin
     *
     * Output example:
     * array(
     *   'action'=>'quote',
     *   'attribute'=>'shipping_amount',
     *   'operator'=>'to',
     *   'value'=>10
     * )
     * 
     * @return array
     */
    public function toArray();
    
    /**
     * Load action data from an array
     * 
     * Formated as an output of self::toArray()
     *
     * @param array $arr
     * @return self
     */
    public function loadArray($arr);
}