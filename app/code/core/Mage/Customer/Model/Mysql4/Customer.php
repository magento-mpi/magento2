<?php
/**
 * Customer model
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Customer extends Mage_Customer_Model_Customer
{
    /**
     * These made static to avoid saving in object
     *
     * @var string
     */
    static protected $_customerTable;
    static protected $_read;
    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
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
            $this->load($authAdapter->getResultRowObject()->customer_id);
        } else {
            return false;
        }
        return $this;
    }

    /**
     * Get row from database table
     *
     * @param   int $rowId
     */
    public function load($customerId)
    {
        $select = self::$_read->select()->from(self::$_customerTable)
            ->where(self::$_read->quoteInto("customer_id=?", $customerId));
        
        $this->setData(self::$_read->fetchRow($select));
    }    
    
    /**
     * Save row in database table
     *
     * @return  integer|false
     */
    public function save()
    {
        self::$_write->beginTransaction();

        try {

            if ($this->getCustomerId()) {
                //$condition = self::$_write->quoteInto('customer_id=?', $this->getCustomerId());
                //self::$_write->update(self::$_customerTable, $this->getData(), $condition);
            } else { 
                self::$_write->insert(self::$_customerTable, $this->_prepareSaveData());
                $this->setCustomerId(self::$_write->lastInsertId());
            }

            self::$_write->commit();
        }
        catch (Exception $e){
            self::$_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE001'));
        }
        
        return $this;
    }
    
    private function _prepareSaveData()
    {
        $data = $this->__toArray(array('email', 'firstname', 'lastname'));
        return $data;
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
        self::$_write->delete(self::$_customerTable, $condition);
        return $this;
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
        
        $customerModel = Mage::getModel('customer', 'customer');
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
        $customerModel = Mage::getModel('customer', 'customer');
        $customer = $customerModel->loadByEmail($arrData['customer_email']);

        if ($customer->getCustomerId() && ($customer->getCustomerId() != Mage::getSingleton('customer_model', 'session')->getCustomerId())) {
            $this->_message = 'E-Mail Address already exists';
            return false;
        }

        return true;
    }
    
    public function validate($withPasswordConfirm=false)
    {
        $arrData= $this->_prepareArray($this->getData(), array('firstname', 'lastname', 'email', 'password'));
        
        //$this->_data = array();
        $this->_data['customer_email']      = $arrData['email'];
        $this->_data['customer_pass']       = $arrData['password'];
        $this->_data['customer_firstname']  = $arrData['firstname'];
        $this->_data['customer_lastname']   = $arrData['lastname'];
        $this->_data['customer_type_id']    = 1; // TODO: default or defined customer type
        
        if ($this->customerId) {
            
        }
        else {
            
        }
        $testCustomer = Mage::getModel('customer', 'customer');
        $testCustomer->loadByEmail($arrData['email']);
        if ($testCustomer->getCustomerId()) {
            $this->_message = 'Your E-Mail Address already exists in our records - please log in with the e-mail address or create an account with a different address';
            return false;
        }
        return true;
    }
    
    public function validatePassword($password)
    {
        return $this->getCustomerPass()===$this->_hashPassword($password);
    }
    
}