<?php
/**
 * Customer model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Resource_Model_Mysql4_Customer extends Mage_Customer_Resource_Model_Mysql4 implements Mage_Core_Resource_Model_Db_Table_Interface
{
    protected $_customerTable;
    
    public function __construct() 
    {
        parent::__construct();
        
        $this->_customerTable = $this->_getTableName('customer_setup', 'customer');
    }
    
    /**
     * Authenticate customer
     *
     * @param   string $username
     * @param   string $password
     * @return  false || object
     */
    public function authenticate($username, $password)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable($this->_read, $this->_customerTable, 'customer_email', 'customer_pass', 'md5(?)');
        $result = $authAdapter->setIdentity($username)->setCredential($password)->authenticate();
        
        if (Zend_Auth_Result::SUCCESS===$result->getCode()) {
            return $authAdapter->getResultRowObject();
        } else {
            return false;
        }
    }
    
    public function setDefaultAddress($customerId, $addressId)
    {
        $arrData = array('default_address_id'=>$addressId);
        return $this->update($arrData, $customerId);
    }
    
    public function changePassword($customerId, $newPassword)
    {
        $data = array('customer_pass' => $newPassword);
        return $this->update($data, $customerId);
    }
    
    public function checkPassword($customerId, $password)
    {
        $arrData = array(
            'id'    => $customerId,
            'pass'  => $this->_encodePassword($password)
        );
        
        $sql = "SELECT customer_id FROM $this->_customerTable WHERE customer_id=:id AND customer_pass=:pass";
        return $this->_read->fetchOne($sql, $arrData);
    }
    
    public function getByEmail($customerEmail)
    {
        $arrData = array(
            'email'    => $customerEmail,
        );
        
        $sql = "SELECT * FROM $this->_customerTable WHERE customer_email=:email";
        return new Varien_DataObject($this->_read->fetchRow($sql, $arrData));
    }

    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  int || false
     */
    public function insert($data)
    {
        if (isset($data['customer_pass'])) {
            $data['customer_pass'] = $this->_encodePassword($data['customer_pass']);
        }
        
        if ($this->_write->insert($this->_customerTable, $data)) {
            return $this->_write->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update row in database table
     *
     * @param   array $data
     * @param   int   $rowId
     * @return  int
     */
    public function update($data, $rowId)
    {
        if (isset($data['customer_pass'])) {
            $data['customer_pass'] = $this->_encodePassword($data['customer_pass']);
        }
        
        $condition = $this->_write->quoteInto('customer_id=?', $rowId);
        return $this->_write->update($this->_customerTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($rowId)
    {
        $condition = $this->_write->quoteInto('customer_id=?', $rowId);
        return $this->_write->delete($this->_customerTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getRow($rowId)
    {
        $sql = "SELECT * FROM $this->_customerTable WHERE customer_id=:customer_id";
        return new Varien_DataObject($this->_read->fetchRow($sql, array('customer_id'=>$rowId)));
    }    
    
    protected function _encodePassword($password)
    {
        return md5($password);
    }
}