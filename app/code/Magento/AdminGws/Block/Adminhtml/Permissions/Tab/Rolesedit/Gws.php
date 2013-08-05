<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Websites fieldset for admin roles edit tab
 */
class Magento_AdminGws_Block_Adminhtml_Permissions_Tab_Rolesedit_Gws extends Magento_Backend_Block_Template
{
    /**
     * Check whether role assumes all websites permissions
     *
     * @return bool
     */
    public function getGwsIsAll()
    {
        if (!$this->canAssignGwsAll()) {
            return false;
        }

        if (!Mage::registry('current_role')->getId()) {
            return true;
        }

        return Mage::registry('current_role')->getGwsIsAll();
    }

    /**
     * Get the role object
     *
     * @return Magento_User_Model_Role
     */
    public function getRole()
    {
        return Mage::registry('current_role');
    }

    /**
     * Check an ability to create 'no website restriction' roles
     *
     * @return bool
     */
    public function canAssignGwsAll()
    {
        return Mage::getSingleton('Magento_AdminGws_Model_Role')->getIsAll();
    }

    /**
     * Gather disallowed store group ids and return them as Json
     *
     * @return string
     */
    public function getDisallowedStoreGroupsJson()
    {
        $result = array();
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $groupId = $group->getId();
                if (!Mage::getSingleton('Magento_AdminGws_Model_Role')->hasStoreGroupAccess($groupId)) {
                    $result[$groupId] = $groupId;
                }
            }
        }
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($result);
    }
}
