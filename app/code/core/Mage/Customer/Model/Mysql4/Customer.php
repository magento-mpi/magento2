<?php
/**
 * Customer model
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Customer extends Mage_Customer_Customer
{
    /**
     * These made static to avoid saving in object
     *
     * @var string
     */
    static protected $_customerTable;
    static protected $_read;
    static protected $_write;
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        self::$_customerTable = Mage::registry('resources')->getTableName('customer', 'customer');
        self::$_read = Mage::registry('resources')->getConnection('customer_read');
        self::$_write = Mage::registry('resources')->getConnection('customer_write');
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
        $authAdapter = new Zend_Auth_Adapter_DbTable(self::$_read, self::$_customerTable, 'customer_email', 'customer_pass', 'md5(?)');
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
        
        $sql = "SELECT customer_id FROM self::$_customerTable WHERE customer_id=:id AND customer_pass=:pass";
        return self::$_read->fetchOne($sql, $arrData);
    }
    
    public function loadByEmail($customerEmail)
    {
        $arrData = array(
            'email'    => $customerEmail,
        );
        
        $select = self::$_read->select()->from(self::$_customerTable)
            ->where(self::$_read->quoteInto("customer_email=?", $customerEmail));
            
        $this->setData(self::$_read->fetchRow($select, $arrData));
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
        
        if (self::$_write->insert(self::$_customerTable, $data)) {
            return self::$_write->lastInsertId();
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
        
        $condition = self::$_write->quoteInto('customer_id=?', $this->getCustomerId());
        return self::$_write->update(self::$_customerTable, $data, $condition);
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
        $condition = self::$_write->quoteInto('customer_id=?', $customerId);
        return self::$_write->delete(self::$_customerTable, $condition);
    }
    
    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function loadByCustomerId($customerId)
    {
        $select = self::$_read->select()->from(self::$_customerTable)
            ->where(self::$_read->quoteInto("customer_id=?", $customerId));
        $this->setData(self::$_read->fetchRow($select));
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
        $customer = $customerModel->loadByEmail($arrData['email']);
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
        $customer = $customerModel->loadByEmail($arrData['customer_email']);

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