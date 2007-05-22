<?php

/**
 * Class describing db table resource entity
 *
 */
class Mage_Core_Model_Resource_Entity_Table extends Mage_Core_Model_Resource_Entity_Abstract
{
    function getTable()
    {
        return $this->getConfig('table');
    }
}