<?php

class Mage_Api2_Model_Auth_Admin
{
    public function authenticate(Zend_Controller_Request_Http $request, $temp = 1)
    {
        $userId = $temp;

        return $userId;
    }
}
