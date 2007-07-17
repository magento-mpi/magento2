<?php
class Mage_Adminhtml_Block_Permissions_Tab_Rolesedit extends Mage_Adminhtml_Block_Widget_Form {
    public function __construct() {
        parent::__construct();

        $rid = Mage::registry('controller')->getRequest()->getParam('rid', false);

        $resources = Mage::getModel("permissions/roles")->getResourcesList();

        $rules_set = Mage::getResourceModel("permissions/rules_collection")
        				->getByRoles($rid)
        				->load();

        //adding additional data to resources array
        foreach ($rules_set->getItems() as $item) {
        	if (array_key_exists(strtolower($item->getResource_id()), $resources)) {
        		$resources[$item->getResource_id()]['checked'] = true;
        	}
        }

        $this->setTemplate('adminhtml/permissions/rolesedit.phtml')
        	->assign('resources', $resources);
    }
}