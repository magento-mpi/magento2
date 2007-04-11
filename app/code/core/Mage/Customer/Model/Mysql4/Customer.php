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
        $arrData = array(
            'email'    => $customerEmail,
        );
        
        $select = self::$_read->select()->from(self::$_customerTable)
            ->where(self::$_read->quoteInto("email=?", $customerEmail));
        $this->setData(self::$_read->fetchRow($select, $arrData));
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

            if ($this->getCustomerId()) {
                //$condition = self::$_write->quoteInto('customer_id=?', $this->getCustomerId());
                //self::$_write->update(self::$_customerTable, $this->getData(), $condition);
            } else { 
                
                self::$_write->insert(self::$_customerTable, $this->_prepareSaveData());
                $this->setCustomerId(self::$_write->lastInsertId());
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
        $data = $this->__toArray(array('email', 'firstname', 'lastname'));
        // TODO: Zend_Validate for fields
        
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
    
    public function validatePassword($password)
    {
        return $this->getCustomerPass()===$this->_hashPassword($password);
    }
    
}