<?php
/**
 * Roles collection
 *
 * @package    Mage
 * @subpackage Admin
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Admin_Model_Mysql4_Acl_Role_Collection extends Varien_Data_Collection_Db
{
    protected $_roleTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('admin_read'));
        $this->_roleTable = Mage::getSingleton('core/resource')->getTableName('admin_resource', 'role');
        $this->_sqlSelect->from($this->_roleTable);
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('admin/acl_role'));
    }
}