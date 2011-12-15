<?php

interface Mage_Api2_Model_Acl_Local_Interface
{
    public function isAllowed($userId, $operation);
}
