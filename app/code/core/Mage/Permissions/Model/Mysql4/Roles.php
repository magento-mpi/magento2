<?php
class Mage_Permissions_Model_Mysql4_Roles {
	protected $_usersTable;
	protected $_roleTable;
	protected $_ruleTable;
	protected $_usersRelTable;

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
        $this->_usersRelTable         = $resources->getTableName('permissions/admin_users_in_roles');

        $this->_read    = $resources->getConnection('permissions_read');
        $this->_write   = $resources->getConnection('permissions_write');
    }

    public function load($roleId) {
        if ($roleId) {
        	$row = $this->_read->fetchRow("SELECT * FROM {$this->_roleTable} WHERE role_id = {$roleId}");
        	return $row;
    	} else {
    		return array();
    	}
    }

    public function save(Mage_Permissions_Model_Roles $role) {
    	if ($role->getPid() > 0) {
			$row = $this->load($role->getPid());
    	} else {
			$row = array('tree_level' => 0);
    	}

    	if ($role->getId()) {
    		$this->_write->update($this->_roleTable, array('parent_id' => $role->getPid(),
    													   'tree_level' => $row['tree_level'] + 1,
    													   'role_name' => $role->getName(),

    													   ), "role_id = {$role->getId()}");
    	} else {
    		$this->_write->insert($this->_roleTable, array('parent_id' => $role->getPid(),
    													   'tree_level' => $row['tree_level'] + 1,
    													   'role_name' => $role->getName(),
    													   'role_type' => $role->getRoleType(),
    													   ));
    		$role->setId($this->_write->lastInsertId());
    	}

    	return $role->getId();
    }

    public function delete(Mage_Permissions_Model_Roles $role) {
    	$this->_write->beginTransaction();

        try {
	    	$this->_write->delete($this->_roleTable, "role_id={$role->getId()}");
	    	$this->_write->delete($this->_ruleTable, "role_id={$role->getId()}");

	    	$this->_write->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e){
            $this->_write->rollBack();
        }
    }
}
?>