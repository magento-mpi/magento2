<?php

/**
 * Entity attribute select source interface
 * 
 * Source is providing the selection options for user interface
 *
 */
interface Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    public function getAllOptions();

    public function getOptionText($value);
}