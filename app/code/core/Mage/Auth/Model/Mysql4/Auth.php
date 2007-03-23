<?php

class Mage_Auth_Model_Mysql4_Auth extends Mage_Auth_Model_Mysql4 
{
    protected $_authAdapter = null;
    
    public function __construct()
    {
        parent::__construct();
        
        $userTable = $this->_getTableName('auth_setup', 'user');
        $this->_authAdapter = new Zend_Auth_Adapter_DbTable($this->_read, $userTable, 'username', 'password', 'md5(?)');
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