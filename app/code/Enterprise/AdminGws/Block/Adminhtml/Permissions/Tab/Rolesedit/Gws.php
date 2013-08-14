<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Websites fieldset for admin roles edit tab
 */
class Enterprise_AdminGws_Block_Adminhtml_Permissions_Tab_Rolesedit_Gws extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

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
     * @return Mage_User_Model_Role
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
        return Mage::getSingleton('Enterprise_AdminGws_Model_Role')->getIsAll();
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
                if (!Mage::getSingleton('Enterprise_AdminGws_Model_Role')->hasStoreGroupAccess($groupId)) {
                    $result[$groupId] = $groupId;
                }
            }
        }
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }

    /**
     * @return Mage_Core_Model_StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
