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
    protected $_read;
    protected $_write;
    protected $_customerTable;

    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_read =  $resource->getConnection('customer_read');
        $this->_write = $resource->getConnection('customer_write');
        $this->_customerTable = $resource->getTableName('customer/customer');
    }
    
    public function getIdFieldName()
    {
        return 'customer_id';
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
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            $this->_read, 
            $this->_customerTable, 
            'email', 
            'password', 
            'md5(?)'
        );
        
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
        $select = $this->_read->select()
            ->from($this->_customerTable)
            ->where($this->_read->quoteInto("customer_id=?", $customerId));
        return $this->_read->fetchRow($select);
    }    

    public function loadByEmail($customerEmail)
    {
        $select = $this->_read->select()
            ->from($this->_customerTable)
            ->where($this->_read->quoteInto("email=?", $customerEmail));
        return $this->_read->fetchRow($select);
    }
    
    /**
     * Save row in database table
     *
     * @return  integer|false
     */
    public function save(Mage_Customer_Model_Customer $customer)
    {
        $this->_write->beginTransaction();

        try {
            $data = $this->_prepareSaveData($customer);
            
            if ($customer->getId()) {
                $condition = $this->_write->quoteInto('customer_id=?', $customer->getId());
                $this->_write->update($this->_customerTable, $data, $condition);
            } else { 
                $data['created_at'] = now();
                $data['customer_type_id'] = 1;
                $this->_write->insert($this->_customerTable, $data);
                $customer->setId($this->_write->lastInsertId());
            }

            // Save customer addresses
            $customer->getAddressCollection()->walk('setCustomerId', $customer->getId());
            $customer->getAddressCollection()->walk('save', false);
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            Mage::throwException('saving customer error');
            //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE001'));
        }
        
        return $customer;
    }
    
    private function _prepareSaveData(Mage_Customer_Model_Customer $customer)
    {
        $data['customer_id'] = $customer->getId();
        $data['email']       = $customer->getEmail();
        $data['firstname']   = $customer->getFirstname();
        $data['lastname']    = $customer->getLastname();
        $data['store_balance'] = $customer->getStoreBalance();
        $data['password']    = $customer->getPasswordHash();
        
        // Check uniq email
        $testCustomer = Mage::getModel('customer/customer')->loadByEmail($data['email']);
        
        if ($testCustomer->getId()) {
            if ($customer->getId()) {
                if ($testCustomer->getId() != $customer->getId()) {
                    Mage::throwException('customer email already exist', 'customer/session');
                    /*throw Mage::exception('Mage_Customer')
                        ->addMessage(Mage::getModel('customer/message')->error('CSTE002'));*/
                }
            }
            else {
                Mage::throwException('customer email already exist', 'customer/session');
                /*throw Mage::exception('Mage_Customer')
                    ->addMessage(Mage::getModel('customer/message')->error('CSTE003'));*/
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
        if (!$customerId) {
            Mage::throwException('invalid customer id');
            /*throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE009'));*/
        }
        
        $condition = $this->_write->quoteInto('customer_id=?', $customerId);
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_customerTable, $condition);
            $this->_write->commit();
        }
        catch (Exception $e){
            Mage::throwException('can not delete customer');
            /*throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE010'));*/
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
                Mage::throwException('current customer password is empty');
                //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE005'));
            }
            if (!$this->_checkPassword($customerId, $data['current_password'])) {
                Mage::throwException('invalid current password');
                //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE006'));
            }
        }
        
        if ($data['password'] != $data['confirmation']) {
            Mage::throwException('new passwords do not match');
            //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE007'));
        }
        
        $this->_write->beginTransaction();
        try {
            $condition = $this->_write->quoteInto('customer_id=?', $customerId);
            $data = array('password'=>$this->hashPassword($data['password']));
            $this->_write->update($this->_customerTable, $data, $condition);
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE008'));
            Mage::throwException('updating the password error');
        }
        
        return $this;
    }
    
    protected  function _checkPassword($customerId, $password)
    {
        $arrData = array(
            'id'    => $customerId,
            'pass'  => $this->hashPassword($password)
        );
        
        $sql = 'SELECT customer_id FROM ' . $this->_customerTable . ' WHERE customer_id=:id AND password=:pass';
        return $this->_read->fetchOne($sql, $arrData);
    }
    
    public function hashPassword($password)
    {
        return md5($password);
    }
}