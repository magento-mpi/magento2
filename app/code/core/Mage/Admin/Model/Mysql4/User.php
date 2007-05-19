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
    
    public function load($userId)
    {
        $select = $this->_read->select()->from($this->_userTable)
            ->where($this->_read->quoteInto("user_id=?", $userId));
        return $this->_read->fetchRow($select);
    }
    
    public function loadByUsername($username)
    {
        $select = $this->_read->select()->from($this->_userTable)
            ->where($this->_read->quoteInto("username=?", $username));
        return $this->_read->fetchRow($select);
    }

    public function save(Mage_Admin_Model_User $user)
    {
        $this->_write->beginTransaction();

        try {
            $data = array(
                'firstname' => $user->getFirstname(),
                'lastname'  => $user->getLastname(),
                'email'     => $user->getEmail(),
                'username'  => $user->getUsername(),
                'modified'  => new Zend_Db_Expr('NOW()'),
            );
            
            if ($user->getPassword()) {
                $data['password'] = md5($user->getPassword());
            }
            
            if ($user->getId()) {
                $condition = $this->_write->quoteInto('user_id=?', $user->getId());
                $this->_write->update($this->_userTable, $data, $condition);
            } else { 
                $data['created'] = new Zend_Db_Expr('NOW()');
                $this->_write->insert($this->_userTable, $data);
                $user->setUserId($this->_write->lastInsertId());
            }

            $this->_write->commit();
        }
        catch (Mage_Core_Exception $e)
        {
            throw $e;
        }
        
        return $user;
    }
    
    public function delete()
    {
            
    }
}