<?php

class Mage_Eav_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {

        }
        return $this->_options;
    }
}