<?php
/**
 * Customer model
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Customer
{
    static protected $_customerTable;

    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_write;
    
    public function __construct() 
    {
        self::$_customerTable = Mage::registry('resources')->getTableName('customer_resource', 'customer');
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
    public function authenticate(Mage_Customer_Model_Customer $customer, $username, $password)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(self::$_read, self::$_customerTable, 'email', 'password', 'md5(?)');
        $result = $authAdapter
            ->setIdentity($username)
            ->setCredential($password)
            ->authenticate();
        
        if (Zend_Auth_Result::SUCCESS===$result->getCode()) {
            $customer->load($authAdapter->getResultRowObject()->customer_id);
        } else {
            return false;
        }
        return true;
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
        return self::$_read->fetchRow($select);
    }    

    public function loadByEmail($customerEmail)
    {
        $select = self::$_read->select()->from(self::$_customerTable)
            ->where(self::$_read->quoteInto("email=?", $customerEmail));
        return self::$_read->fetchRow($select);
    }
    
    /**
     * Save row in database table
     *
     * @return  integer|false
     */
    public function save(Mage_Customer_Model_Customer $customer)
    {
        self::$_write->beginTransaction();

        try {
            $data = $this->_prepareSaveData($customer);
            
            if ($customer->getId()) {
                $condition = self::$_write->quoteInto('customer_id=?', $customer->getId());
                self::$_write->update(self::$_customerTable, $data, $condition);
            } else { 
                
                self::$_write->insert(self::$_customerTable, $data);
                $customer->setCustomerId(self::$_write->lastInsertId());
                
                // Save customer addresses
                $customer->getAddressCollection()->walk('setCustomerId', $customer->getId());
                $customer->getAddressCollection()->walk('save', false);
            }

            self::$_write->commit();
        }
        catch (Mage_Core_Exception $e)
        {
            throw $e;
        }
        catch (Exception $e){
            self::$_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE001'));
        }
        
        return $customer;
    }
    
    private function _prepareSaveData(Mage_Customer_Model_Customer $customer)
    {
        $data['customer_id'] = $customer->getId();
        $data['email']       = $customer->getEmail();
        $data['firstname']   = $customer->getFirstname();
        $data['lastname']    = $customer->getLastname();
        
        // TODO: Zend_Validate for fields
        
        if ($customer->getPassword()) {
            $data['password'] = $this->hashPassword($customer->getPassword());
        }
        
        // Check uniq email
        $testCustomer = Mage::getModel('customer', 'customer')->loadByEmail($data['email']);
        
        if ($testCustomer->getId()) {
            if ($customer->getId()) {
                if ($testCustomer->getId() != $customer->getId()) {
                    throw Mage::exception('Mage_Customer')
                        ->addMessage(Mage::getModel('customer', 'message')->error('CSTE002'));
                }
            }
            else {
                throw Mage::exception('Mage_Customer')
                    ->addMessage(Mage::getModel('customer', 'message')->error('CSTE003'));
            }
        }
        return $data;
    }
    
    /**
     * Delete row from database table
     *
     * @param   int $rowId
     */
    public function delete($customerId)
    {
        if (is_null($customerId)) {
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer', 'message')->error('CSTE009'));
        }
        
        $condition = self::$_write->quoteInto('customer_id=?', $customerId);
        self::$_write->beginTransaction();
        try {
            self::$_write->delete(self::$_customerTable, $condition);
            self::$_write->commit();
        }
        catch (Exception $e){
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer', 'message')->error('CSTE010'));
        }
        return true;
    }
    
    /**
     * Change customer password
     * $data = array(
     *      ['password']
     *      ['confirmation']
     *      ['current_password']
     * )
     * 
     * @param   array $data
     * @param   bool $checkCurrent
     * @return  this
     */
    public function changePassword($customerId, $data, $checkCurrent=true)
    {
        if ($checkCurrent) {
            if (empty($data['current_password'])) {
                throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE005'));
            }
            if (!$this->_checkPassword($customerId, $data['current_password'])) {
                throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE006'));
            }
        }
        
        if ($data['password'] != $data['confirmation']) {
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE007'));
        }
        
        self::$_write->beginTransaction();
        try {
            $condition = self::$_write->quoteInto('customer_id=?', $customerId);
            $data = array('password'=>$this->hashPassword($data['password']));
            self::$_write->update(self::$_customerTable, $data, $condition);
            self::$_write->commit();
        }
        catch (Exception $e){
            self::$_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer', 'message')->error('CSTE008'));
        }
        
        return $this;
    }
    
    protected  function _checkPassword($customerId, $password)
    {
        $arrData = array(
            'id'    => $customerId,
            'pass'  => $this->hashPassword($password)
        );
        
        $sql = 'SELECT customer_id FROM ' . self::$_customerTable . ' WHERE customer_id=:id AND password=:pass';
        return self::$_read->fetchOne($sql, $arrData);
    }
    
    public function hashPassword($password)
    {
        return md5($password);
    }
}