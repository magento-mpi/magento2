<?php
/**
 * Users collection
 *
 * @package    Mage
 * @subpackage Admin
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Admin_Model_Mysql4_User_Collection extends Varien_Data_Collection_Db
{
    protected $_userTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('admin_read'));
        $this->_userTable = Mage::getSingleton('core/resource')->getTableName('admin_resource', 'user');
        $this->_sqlSelect->from($this->_userTable);
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('admin/user'));
    }
}