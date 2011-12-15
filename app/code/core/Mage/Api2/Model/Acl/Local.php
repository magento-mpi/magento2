<?php

class Mage_Api2_Model_Acl_Local implements Mage_Api2_Model_Acl_Local_Interface
{
    public function isAllowed($userId, $operation)
    {
        $resourceId = $this->getId();
        
        //TODO add implementation
        $isAllowed = true;

        return $isAllowed;
    }
}
