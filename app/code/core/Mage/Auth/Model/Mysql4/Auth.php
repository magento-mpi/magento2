<?php

class Mage_Auth_Model_Mysql4_Auth
{
    protected static $_read = null;
    protected static $_write = null;
    protected static $_userTable = null;

    protected $_authAdapter = null;
    
    public function __construct()
    {
        self::$_read = Mage::registry('resources')->getConnection('auth_read');
        self::$_write = Mage::registry('resources')->getConnection('auth_write');
        self::$_userTable = Mage::registry('resources')->getTableName('auth', 'user');

        $this->_authAdapter = new Zend_Auth_Adapter_DbTable(self::$_read, self::$_userTable, 'username', 'password', 'md5(?)');
    }
    
    public function authenticate($username, $password)
    {
        $result = $this->_authAdapter->setIdentity($username)->setCredential($password)->authenticate();
        
        if (Zend_Auth_Result::SUCCESS===$result->getCode()) {
            return $this->_authAdapter->getResultRowObject();
        } else {
            return false;
        }
    }
}