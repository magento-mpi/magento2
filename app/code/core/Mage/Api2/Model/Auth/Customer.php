<?php

class Mage_Api2_Model_Auth_Customer
{
    public function authenticate(Zend_Controller_Request_Http $request, $temp = 2)
    {
        $userId = $temp;

        return $userId;
    }
}
