<?php
class Mage_Permissions_Model_Mysql4_Rules {
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
    
    public function load() {
        
    }
    
    public function save() {    	
       
    }  
    
    /*
    Mage::getModel("permissions/rules")
    		->setRoleId($rid)
    		->setResources($this->getRequest()->getParam('resource', false))
    		->saveRel();
    		
    		rule_id, role_type, role_id, resource_id, privileges, assert_id
	*/
    public function saveRel(Mage_Permissions_Model_Rules $rule) {
    	$this->_write->beginTransaction();

        try {           	            	
        	$roleId = $rule->getRoleId()->getData();
        	$roleId = $roleId['id'];
	    	$this->_write->delete($this->_ruleTable, "role_id = {$roleId}");
	    	if ($rule->getResources()) {
		    	foreach ($rule->getResources() as $key => $val) {	    		
		    		$this->_write->insert($this->_ruleTable, array(
			    		'role_type' => 'G',
			    		'resource_id' => $key,
			    		'privileges' => implode(",", $val),
			    		'assert_id' => 0,
			    		'role_id' => $roleId
			    		
			    		));
		    	}
	    	}
	    	
	    	$this->_write->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e){
            $this->_write->rollBack();            
        }
    }
}
?>