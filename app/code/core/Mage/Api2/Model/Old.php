<?php

class Mage_Api2_Model_Old
{
    public static function authenticate($accessKey)
    {
        $session = new Mage_Api_Model_Session;
        //$session = Mage::getSingleton('api/session')
        $session->setSessionId($accessKey);
        $session->init('api', 'api');

        $isLoggedIn = $session->isLoggedIn($accessKey);

        return $isLoggedIn;
    }

    public static function getTestAccessKey()
    {
        $username = 'api01';
        $apiKey = '123123q';

        $session = new Mage_Api_Model_Session;
        $session->init('api', 'api');
        $session->login($username, $apiKey);
        $accessKey = $session->getSessionId();

        return $accessKey;

    }

    public function isAdminAllowed($resourceType, $operation, $sessionId)
    {
        $session = new Mage_Api_Model_Session;
        $session->setSessionId($sessionId);
        $session->init('api', 'api');

        $user = new Mage_Api_Model_User;
        $user->loadBySessId($sessionId);

        try {
            $aclResource = new Mage_Api_Model_Resource_Acl;
            $isAllowed = $aclResource->loadAcl()->isAllowed($user->getAclRole(), $resourceType, $operation);

            echo __FILE__;
            echo '<pre>';
            var_dump($isAllowed);
            echo '</pre>';
            exit;

        } catch (Exception $e) {
            $isAllowed = false;

            echo __FILE__;
            echo '<pre>';
            var_dump($e->getMessage());
            echo '</pre>';
            exit;
        }


        return $isAllowed;

        return $this;
    }

}
