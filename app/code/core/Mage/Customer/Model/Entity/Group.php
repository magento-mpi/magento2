<?
/**
 * Customer group model
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Customer_Model_Entity_Group
{
    /**
     * Customers group table name
     *
     * @var string
     */
    protected $_groupTable;
    
    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    /**
     * Constructor
     * 
     * Model initialization
     */
    public function __construct() 
    {
        $this->_groupTable = Mage::getSingleton('core/resource')->getTableName("customer/customer_group");
        $this->_read = Mage::getSingleton('core/resource')->getConnection('customer_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('customer_write');
    }
    
    /**
     * Load group information from DB
     * 
     * @param   int $groupId
     * @return  array
     */
    public function load($groupId) 
    {
        $select = $this->_read->select()->from($this->_groupTable)
            ->where('customer_group_id=?',$groupId);
        
        return $this->_read->fetchRow($select);
    }
    
    /**
     * Save group information to DB
     *
     * @param   Mage_Customer_Model_Group $group
     * @return  Mage_Customer_Model_Group
     * @throws  Mage_Customer_Exception
     */
    public function save(Mage_Customer_Model_Group $group) 
    {
        $this->_write->beginTransaction();
        
        try {
            $data = $this->_prepareSaveInformation($group);
            if ($group->getId())  {
                $this->_write->update($this->_groupTable, $data, 
                                      $this->_write->quoteInto('customer_group_id=?', $group->getId()));
            } else {
                $this->_write->insert($this->_groupTable, $data);
                $group->setId($this->_write->lastInsertId());
            }
            $this->_write->commit();
        }
        catch(Mage_Core_Exception $e) {
            throw $e;
        }
        catch(Exception $e) {
            $this->_write->rollBack();
            throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE024'));
        }
        return $group;
    }
    
    /**
     * Prepares group information for saving
     *
     * @param   Mage_Customer_Model_Group $group
     * @return  array
     * @throws  Mage_Customer_Exception
     */
    protected function _prepareSaveInformation(Mage_Customer_Model_Group $group) 
    {
        $data = array();
        $data['customer_group_id'] = $group->getId();
        $data['customer_group_code'] = $group->getCode();
        
        if (trim($group->getCode()) == '') {
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE025'));
        }
        
        return $data;
    }
    
    /**
     * Delete customer group from DB.
     *
     * @param   int $groupId
     * @throws  Mage_Customer_Exception
     */
    public function delete($groupId)
    {
        $groupId = (int)$groupId;
        if (!$groupId) {
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE026'));
        }
        
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_groupTable, $this->_write->quoteInto('customer_group_id=?',$groupId));
            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            throw Mage::exception('Mage_Customer')
                ->addMessage(Mage::getModel('customer/message')->error('CSTE027'));
        }
    }
}
