<?php
class Mage_Permissions_Model_Mysql4_Users {
	protected $_usersTable;
	protected $_roleTable;
	protected $_ruleTable;

    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    public function __construct() {
        $resources = Mage::getSingleton('core/resource');

        $this->_usersTable        = $resources->getTableName('permissions/admin_user');
        $this->_roleTable         = $resources->getTableName('permissions/admin_role');
        $this->_ruleTable         = $resources->getTableName('permissions/admin_rule');

        $this->_read    = $resources->getConnection('permissions_read');
        $this->_write   = $resources->getConnection('permissions_write');
    }

    public function load($uid) {
    	if ($uid) {
        	return $this->_read->fetchRow("SELECT * FROM {$this->_usersTable} WHERE user_id = {$uid}");
    	} else {
    		return array();
    	}
    }

    public function save(Mage_Permissions_Model_Users $user) {
    	if ($user->getId()) {
    		$data = array(
	       		'firstname' => $user->getFirstname(),
	       		'lastname' => $user->getLastname(),
	       		'email' => $user->getEmail(),
	       );

	       if( $user->getUsername() ) {
	           $data['username'] = $user->getUsername();
	       }

	       if ($user->getPassword()) {
	       		$data['password'] = Mage::getModel("permissions/users")->encodePwd($user->getPassword());
	       }

	       $this->_write->update($this->_usersTable, $data, "user_id = {$user->getId()}");
    	} else {
    		$data = array(
	       		'firstname' => $user->getFirstname(),
	       		'lastname' => $user->getLastname(),
	       		'email' => $user->getEmail(),
	       		);

            if( $user->getUsername() ) {
                $data['username'] = $user->getUsername();
            }

	       	if ($user->getPassword()) {
	       		$data['password'] = Mage::getModel("permissions/users")->encodePwd($user->getPassword());
	        }
    		$this->_write->insert($this->_usersTable, $data);
	       	$user->setId($this->_write->lastInsertId());
    	}
    }

    public function add(Mage_Permissions_Model_Users $user) {
    	if ($user->getRoleId() > 0) {
    		$row = Mage::getModel('permissions/roles')->load($user->getRoleId());
    	} else {
    		$row = array('tree_level' => 0);
    	}

    	$this->_write->insert($this->_roleTable, array(
	    	'parent_id' => $user->getRoleId(),
	    	'tree_level' => ($row->getTreeLevel() + 1),
	    	'sort_order' => 0,
	    	'role_type' => 'U',
	    	'user_id' => $user->getUserId(),
	    	'role_name' => $user->getFirstname()
    	));

    	return $this;
    }

    public function deleteFromRole(Mage_Permissions_Model_Users $users) {
        $condition = $this->_write->quoteInto("{$this->_roleTable}.role_id = ?", $users->getUserId());
    	$this->_write->delete($this->_roleTable, $condition);

    	return $this;
    }

    public function saveRel(Mage_Permissions_Model_Users $user) {
    	$data = array();
    	$ids = $user->getIds();

    	if( !is_array($ids) || count($ids) == 0 ) {
    	    return $user;
    	}

    	$this->_write->beginTransaction();

        try {
	    	$this->_write->delete($this->_roleTable, "user_id = {$user->getUid()}");

	    	foreach ($ids as $id) {
	    		if ($id > 0 && 0) {
		    		$row = $this->load($id);
		    	} else {
		    		$row = array('tree_level' => 0);
		    	}


		    	$this->_write->insert($this->_roleTable, array(
			    	'parent_id' => $id,
			    	'tree_level' => $row['tree_level'] + 1,
			    	'sort_order' => 0,
			    	'role_type' => 'U',
			    	'user_id' => $user->getUid(),
			    	'role_name' => $user->getFirstname()
		    	));
	    	}

	    	$this->_write->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e){
            $this->_write->rollBack();
        }
    }

    public function delete(Mage_Permissions_Model_Users $user) {
    	$this->_write->beginTransaction();

        try {
	    	$this->_write->delete($this->_usersTable, "user_id={$user->getId()}");
	    	$this->_write->delete($this->_roleTable, "user_id={$user->getId()}");
	    	$this->_write->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e){
            $this->_write->rollBack();
        }
    }

    public function roleUserExists($model)
    {
        $select = $this->_read->select();
        $select->from($this->_roleTable);
        $select->where("{$this->_roleTable}.parent_id = {$model->getRoleId()} AND {$this->_roleTable}.user_id = {$model->getUserId()}");
        return $this->_read->fetchRow($select);
    }

    public function userExists($model)
    {
        $select = $this->_read->select();
        $select->from($this->_usersTable);
        $select->where("{$this->_usersTable}.username = '{$model->getUsername()}' AND {$this->_usersTable}.user_id != '{$model->getId()}'");
        return $this->_read->fetchRow($select);
    }
}
?>