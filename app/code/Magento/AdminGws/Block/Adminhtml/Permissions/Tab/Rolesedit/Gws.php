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
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_AdminGws_Model_Role
     */
    protected $_adminGwsRole;

    /**
     * @param Magento_AdminGws_Model_Role $adminGwsRole
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_AdminGws_Model_Role $adminGwsRole,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_adminGwsRole = $adminGwsRole;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($coreData, $context, $data);
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

        if (!$this->_coreRegistry->registry('current_role')->getId()) {
            return true;
        }

        return $this->_coreRegistry->registry('current_role')->getGwsIsAll();
    }

    /**
     * Get the role object
     *
     * @return Magento_User_Model_Role
     */
    public function getRole()
    {
        return $this->_coreRegistry->registry('current_role');
    }

    /**
     * Check an ability to create 'no website restriction' roles
     *
     * @return bool
     */
    public function canAssignGwsAll()
    {
        return $this->_adminGwsRole->getIsAll();
    }

    /**
     * Gather disallowed store group ids and return them as Json
     *
     * @return string
     */
    public function getDisallowedStoreGroupsJson()
    {
        $result = array();
        foreach ($this->_storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $groupId = $group->getId();
                if (!$this->_adminGwsRole->hasStoreGroupAccess($groupId)) {
                    $result[$groupId] = $groupId;
                }
            }
        }
        return $this->_coreData->jsonEncode($result);
    }

    /**
     * Get websites
     *
     * @return Magento_Core_Model_Website[]
     */
    public function getWebsites()
    {
        return $this->_storeManager->getWebsites();
    }
}
