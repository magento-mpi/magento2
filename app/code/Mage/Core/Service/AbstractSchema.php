<?php

class Mage_Core_Service_AbstractSchema extends Varien_Object
{
    public function load($schema)
    {
        if (is_array($schema)) {
            $this->setData($schema);
        }
    }
}
