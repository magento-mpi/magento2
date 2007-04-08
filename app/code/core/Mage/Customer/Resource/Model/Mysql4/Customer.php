<?php
/**
 * Customer model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Resource_Model_Mysql4_Customer extends Mage_Customer_Customer
{
    protected $_customerTable;
    protected $_read;
    protected $_write;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        $this->_customerTable = Mage::registry('resources')->getTableName('customer', 'customer');
        $this->_read = Mage::registry('resources')->getConnection('customer_read');
        $this->_write = Mage::registry('resources')->getConnection('customer_write');
    }
    
    /**
     * Authenticate customer
     *
     * @param   string $username
     * @param   string $password
     * @return  false|object
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
            'pass'  => $this->_hashPassword($password)
        );
        
        $sql = "SELECT customer_id FROM $this->_customerTable WHERE customer_id=:id AND customer_pass=:pass";
        return $this->_read->fetchOne($sql, $arrData);
    }
    
    public function getByEmail($customerEmail)
    {
        $arrData = array(
            'email'    => $customerEmail,
        );
        
        $select = $this->_read->select()->from($this->_customerTable)
            ->where($this->_read->quoteInto("customer_email=?", $customerEmail));
            
        $this->setData($this->_read->fetchRow($select, $arrData));
    }

    /**
     * Insert row in database table
     *
     * @param   array $data
     * @return  integer|false
     */
    public function insert()
    {
        $data = $this->getData();
        if (isset($data['customer_pass'])) {
            $data['customer_pass'] = $this->_hashPassword($data['customer_pass']);
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
    public function update()
    {
        $data = $this->getData();
        if (isset($data['customer_pass'])) {
            $data['customer_pass'] = $this->_hashPassword($data['customer_pass']);
        }
        
        $condition = $this->_write->quoteInto('customer_id=?', $this->getCustomerId());
        return $this->_write->update($this->_customerTable, $data, $condition);
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($customerId=null)
    {
        if (is_null($customerId)) {
            $customerId = $this->getCustomerId();
        }
        $condition = $this->_write->quoteInto('customer_id=?', $customerId);
        return $this->_write->delete($this->_customerTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function getByCustomerId($customerId)
    {
        $select = $this->_read->select()->from($this->_customerTable)
            ->where($this->_read->quoteInto("customer_id=?", $customerId));
        $this->setData($this->_read->fetchRow($select));
    }    
    
    protected function _hashPassword($password)
    {
        return md5($password);
    }
    
    public function validateCreate()
    {
        $data = $this->getData();
        $arrData= $this->_prepareArray($data, array('firstname', 'lastname', 'email', 'password'));
        
        $this->_data = array();
        $this->_data['customer_email']      = $arrData['email'];
        $this->_data['customer_pass']       = $arrData['password'];
        $this->_data['customer_firstname']  = $arrData['firstname'];
        $this->_data['customer_lastname']   = $arrData['lastname'];
        $this->_data['customer_type_id']    = 1; // TODO: default or defined customer type
        
        $customerModel = Mage::getResourceModel('customer', 'customer');
        $customer = $customerModel->getByEmail($arrData['email']);
        if ($customer->getCustomerId()) {
            $this->_message = 'Your E-Mail Address already exists in our records - please log in with the e-mail address or create an account with a different address';
            return false;
        }
        return true;
    }
    
    public function validateUpdate()
    {
        $data = $this->getData();
        $arrData= $this->_prepareArray($data, array('customer_firstname', 'customer_lastname', 'customer_email'));
        $this->_data = $arrData;
        // validate fields.....
        
        // Validate email
        $customerModel = Mage::getResourceModel('customer', 'customer');
        $customer = $customerModel->getByEmail($arrData['customer_email']);

        if ($customer->getCustomerId() && ($customer->getCustomerId() != Mage_Customer_Front::getCustomerId())) {
            $this->_message = 'E-Mail Address already exists';
            return false;
        }

        return true;
    }
    
    public function validateChangePassword($data)
    {
        if (!isset($data['current_password'])) {
            $this->_message = 'Current customer password is empty';
            return false;
        }
        else {
            $customerModel = Mage::getResourceModel('customer', 'customer');
            
            if (!$customerModel->checkPassword(Mage_Customer_Front::getCustomerId(), $data['current_password'])) {
                $this->_message = 'Invalid current password';
                return false;
            }
            if (empty($data['password'])) {
                return false;
            }
        }
        
        $this->_data['password'] = $data['password'];
        return true;
    }
}