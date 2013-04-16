<?php

class Mage_Core_Service_DataSchema extends Varien_Object
{
    public function load($schema)
    {
        if (is_array($schema)) {
            $this->setData($schema);
        } elseif (is_string($schema)) {
            // @todo load schema file by file name reference
            throw new Exception('Need to be implemented: load schema file by file name reference in ' . __METHOD__);
        }
    }
}
