<?php

/**
 * Resource model for admin user authentication
 * 
 * @package     Mage
 * @subpackage  Auth
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Auth_Model_Mysql4_Auth
{
    /**
     * Read resource connection
     *
     * @var mixed
     */
    protected $_read;
    
    /**
     * Write resource connection
     *
     * @var mixed
     */
    protected $_write;
    
    /**
     * User table name 
     *
     * @var string
     */
    protected $_userTable;

    /**
     * Zend auth adapter
     *
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_authAdapter = null;
    
    /**
     * Initializes connections, table names and auth adapter
     *
     */
    public function __construct()
    {
        $this->_read = Mage::registry('resources')->getConnection('auth_read');
        $this->_write = Mage::registry('resources')->getConnection('auth_write');
        $this->_userTable = Mage::registry('resources')->getTableName('auth_resource', 'user');

        $this->_authAdapter = new Zend_Auth_Adapter_DbTable($this->_read, $this->_userTable, 'username', 'password', 'md5(?)');
    }
    
    /**
     * Authenticate user by $username and $password
     *
     * @param string $username
     * @param string $password
     * @return boolean|Object
     */
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