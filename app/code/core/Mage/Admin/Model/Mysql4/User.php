<?php
/**
 * ACL user resource
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Model_Mysql4_User
{
    protected $_userTable;
    protected $_read;
    protected $_write;
    
    public function __construct() 
    {
        $this->_userTable = Mage::registry('resources')->getTableName('auth_resource', 'user');
        $this->_read = Mage::registry('resources')->getConnection('auth_read');
        $this->_write = Mage::registry('resources')->getConnection('auth_write');
    }
    
    public function load()
    {
        
    }
    
    public function save(Mage_Acl_Model_User $user)
    {
        
    }
    
    public function delete()
    {
            
    }
}