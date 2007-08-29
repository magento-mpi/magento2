<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

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
        	if (array_key_exists(strtolower($item->getResource_id()), $resources) && $item->getPermission() == 'allow') {
        		$resources[$item->getResource_id()]['checked'] = true;
        	}
        }

        $this->setTemplate('permissions/rolesedit.phtml')
        	->assign('resources', $resources);
    }
}
