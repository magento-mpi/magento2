<?php

class Mage_Api2_Model_Auth
{
    const USER_TYPE_ADMIN    = 'admin';
    const USER_TYPE_CUSTOMER = 'customer';
    const USER_TYPE_GUEST    = 'guest';

    /**
     * Validate access key
     *
     * @static
     * @param $accessKey
     * @return bool
     */
    public static function authenticate($accessKey)
    {
        //TODO validate access key using OAuth module
        $isLoggedIn = Mage_Api2_Model_Old::authenticate($accessKey);

        return $isLoggedIn;
    }
}
