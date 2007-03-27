<?php
/**
 * Customer model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Customer extends Mage_Customer_Model_Mysql4
{
    protected $_authAdapter;
    
    public function __construct() 
    {
        parent::__construct();
        
        $userTable = $this->_getTableName('customer_setup', 'customer');
        $this->_authAdapter = new Zend_Auth_Adapter_DbTable($this->_read, $userTable, 'customer_email', 'customer_pass', 'md5(?)');
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