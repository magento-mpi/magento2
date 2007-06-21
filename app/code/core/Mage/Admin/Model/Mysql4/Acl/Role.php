<?php
/**
 * ACL role resource
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Model_Mysql4_Acl_Role
{
    protected $_roleTable;
    protected $_read;
    protected $_write;
    
    public function __construct() 
    {
        $this->_roleTable = Mage::getSingleton('core/resource')->getTableName('admin/role');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('admin_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('admin_write');
    }
    
    public function load($roleId)
    {
        $select = $this->_read->select()->from($this->_roleTable)
            ->where($this->_read->quoteInto("role_id=?", $roleId));
        return $this->_read->fetchRow($select);
    }

    public function save(Mage_Admin_Model_Acl_Role $role)
    {
        $data = $role->getData();
        
        $this->_write->beginTransaction();

        try {
            if ($role->getId()) {
                $condition = $this->_write->quoteInto('role_id=?', $role->getRoleId());
                $this->_write->update($this->_roleTable, $data, $condition);
            } else { 
                $data['created'] = new Zend_Db_Expr('NOW()');
                $this->_write->insert($this->_roleTable, $data);
                $role->setRoleId($this->_write->lastInsertId());
            }

            $this->_write->commit();
        }
        catch (Mage_Core_Exception $e)
        {
            $this->_write->rollback();
            throw $e;
        }
        
        return $role;
    }
    
    public function delete()
    {
            
    }
}