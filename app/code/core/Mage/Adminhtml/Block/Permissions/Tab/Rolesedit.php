<?php
class Mage_Adminhtml_Block_Permissions_Tab_Rolesedit extends Mage_Adminhtml_Block_Widget_Form {
    public function __construct() {
        parent::__construct();
        
        $rid = Mage::registry('controller')->getRequest()->getParam('rid', false);
        
        $resources = Mage::getModel("permissions/roles")->getResourcesList();
        $rules_set = Mage::getResourceModel("permissions/rules_collection")
        				->getByRoles($rid)
        				->load();        				
        
        //normalizing resources names and converting values to keys
        $values = array();
        foreach ($resources as & $resource) {
        	$resource = strtolower($resource);
        	$values[] = array();
        }        
        $resources = array_combine($resources, $values);//just simple stupid, but working perfect :)
        
        //adding additional data to resources array
        foreach ($rules_set->getItems() as $item) {
        	if (array_key_exists(strtolower($item->getResource_id()), $resources)) {
        		$resources[$item->getResource_id()] = array('privileges' => explode(",", $item->getPrivileges()));
        	}
        }
        
        $this->setTemplate('adminhtml/permissions/rolesedit.phtml')
        	->assign('resources', $resources);        	
    }
}