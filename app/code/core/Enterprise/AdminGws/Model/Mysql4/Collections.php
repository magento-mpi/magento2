<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_AdminGws
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Collections limiter resource model
 *
 */
class Enterprise_AdminGws_Model_Mysql4_Collections extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');
    }

    public function getRolesOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups)
    {
        $result = array();
        if (!$isAll) {
            $select = $this->_getReadAdapter()->select();
            $select->from(
                $this->getTable('admin/role'),
                array(
                    'role_id',
                    'gws_is_all',
                    'gws_websites',
                    'gws_store_groups'
                )
            );
            $roles = $this->_getReadAdapter()->fetchAll($select);

            foreach ($roles as $role) {
                $roleStoreGroups = Mage::helper('enterprise_admingws')->explodeIds($role['gws_store_groups']);
                $roleWebsites = Mage::helper('enterprise_admingws')->explodeIds($role['gws_websites']);

                $hasAllPermissions = ($role['gws_is_all'] == 1);
                $hasDisallowedWebsites = (array_diff($allowedWebsites, $roleWebsites));
                $hasDisallowedStoreGroups = (array_diff($allowedStoreGroups, $roleStoreGroups));

                if ($hasAllPermissions || $hasDisallowedWebsites || $hasDisallowedStoreGroups) {
                    $result[] = $role['role_id'];
                }
            }
        }

        return $result;
    }

    
    public function getUsersOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups)
    {
        $result = array();

        $limitedRoles = $this->getRolesOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups);
        if ($limitedRoles) {
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('admin/role'), array('user_id'))
                ->where('parent_id IN (?)', $limitedRoles);

            $users = $this->_getReadAdapter()->fetchCol($select);

            if ($users) {
                $result = $users;
            }
        }

        return $result;
    }
}
