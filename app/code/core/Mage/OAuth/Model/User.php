<?php

class Mage_OAuth_Model_User
{
    protected $_accessKey;

    public function __construct($accessKey)
    {
        $this->_accessKey = $accessKey;
    }

    public function getId($temp = 1)
    {
        $userId = $temp;

        return $userId;
    }

    public function getType($temp = Mage_Api2_Model_Auth::USER_TYPE_GUEST)
    {
        $userType = $temp;

        return $userType;
    }

    public function getRole($temp = 'admin')
    {
        $role = $temp;

        return $role;
    }
}
