<?php
class Mage_Permissions_Model_Roles extends Varien_Object {
	public function getResource() {
        return Mage::getResourceSingleton('permissions/roles');
    }
    
    public function load($roleId) {
        $this->setData($this->getResource()->load($roleId));
        return $this;
    }
    
    public function save() {
        $this->getResource()->save($this);
        return $this;        
    }
    
    public function update() {
        $this->getResource()->update($this);
        return $this;        
    }
    
    public function delete() {
        $this->getResource()->delete($this);
        return $this;
    }
    
    public function getCollection() {
        return Mage::getResourceModel('permissions/roles_collection');
    }
    
    public function getResourcesList() {
    	$root = Mage::getBaseDir();
    	$file = $root."/etc/config-compiled.xml";
    	$xml = simplexml_load_string(file_get_contents($file));
    	
    	$resources = array();
    	foreach ($xml->modules->children() as $module) {
    		$resources[] = substr($module->getName(), 5);
    	}
    	
    	sort($resources);
    	return $resources;
    }
}
?>