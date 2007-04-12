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
    
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        self::$_customerTable = Mage::registry('resources')->getTableName('customer', 'customer');
        self::$_read = Mage::registry('resources')->getConnection('customer_read');
        self::$_write = Mage::registry('resources')->getConnection('customer_write');
    }
    
    public function __wakeup()
    {
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
        $authAdapter = new Zend_Auth_Adapter_DbTable(self::$_read, self::$_customerTable, 'email', 'password', 'md5(?)');
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
        return $this;
    }    

    public function loadByEmail($customerEmail)
    {
        $select = self::$_read->select()->from(self::$_customerTable)
            ->where(self::$_read->quoteInto("email=?", $customerEmail));
        $this->setData(self::$_read->fetchRow($select));
        return $this;
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
            $this->_prepareSaveData();
            
            if ($this->getCustomerId()) {
                $condition = self::$_write->quoteInto('customer_id=?', $this->getCustomerId());
                self::$_write->update(self::$_customerTable, $this->getData(), $condition);
            } else { 
                
                self::$_write->insert(self::$_customerTable, $this->getData());
                $this->setCustomerId(self::$_write->lastInsertId());
                $this->getAddressCollection()->walk('setCustomerId', $this->getCustomerId());
                $this->getAddressCollection()->walk('save', false);
            }

            self::$_write->commit();
        }
        catch (Mage_Core_Exception $e)
        {
            throw $e;
        }
        catch (Exception $e){
            self::$_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE001'));
        }
        
        return $this;
    }
    
    private function _prepareSaveData()
    {
        $data = $this->__toArray(array('customer_id', 'email', 'firstname', 'lastname', 'password'));
        
        // TODO: Zend_Validate for fields
        
        if (!empty($data['password'])) {
            $data['password'] = $this->_hashPassword($data['password']);
        }
        else {
            unset($data['password']);
        }
        
        // Check uniq email
        $testCustomer = Mage::getModel('customer', 'customer')->loadByEmail($data['email']);
        
        if ($testCustomer->getCustomerId()) {
            if ($this->getCustomerId()) {
                if ($testCustomer->getCustomerId() != $this->getCustomerId()) {
                    throw Mage::exception('Mage_Customer')
                        ->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE002'));
                }
            }
            else {
                throw Mage::exception('Mage_Customer')
                    ->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE003'));
            }
        }
        $this->setData($data);
        return $this;
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
    public function changePassword($data, $checkCurrent=true)
    {
        if ($checkCurrent) {
            if (empty($data['current_password'])) {
                throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE005'));
            }
            if (!$this->_checkPassword($data['current_password'])) {
                throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE006'));
            }
        }
        
        if ($data['password'] != $data['confirmation']) {
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE007'));
        }
        
        self::$_write->beginTransaction();
        try {
            $condition = self::$_write->quoteInto('customer_id=?', $this->getCustomerId());
            $data = array('password'=>$this->_hashPassword($data['password']));
            self::$_write->update(self::$_customerTable, $data, $condition);
            self::$_write->commit();
        }
        catch (Exception $e){
            self::$_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE008'));
        }
        
        return $this;
    }
    
    protected  function _checkPassword($password)
    {
        $arrData = array(
            'id'    => $this->getCustomerId(),
            'pass'  => $this->_hashPassword($password)
        );
        
        $sql = 'SELECT customer_id FROM ' . self::$_customerTable . ' WHERE customer_id=:id AND password=:pass';
        return self::$_read->fetchOne($sql, $arrData);
    }
}