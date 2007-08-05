<?php

class Mage_Eav_Model_Entity_Attribute_Source_Boolean extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => __('Yes'),
                    'value' =>  1
                ),
                array(
                    'label' => __('No'),
                    'value' =>  0
                ),
            );
        }
        return $this->_options;
    }
}