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
namespace Magento\AdminGws\Block\Adminhtml\Permissions\Tab\Rolesedit;

class Gws extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManager $storeManager,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
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
     * @return \Magento\User\Model\Role
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
        return \Mage::getSingleton('Magento\AdminGws\Model\Role')->getIsAll();
    }

    /**
     * Gather disallowed store group ids and return them as Json
     *
     * @return string
     */
    public function getDisallowedStoreGroupsJson()
    {
        $result = array();
        foreach (\Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $groupId = $group->getId();
                if (!\Mage::getSingleton('Magento\AdminGws\Model\Role')->hasStoreGroupAccess($groupId)) {
                    $result[$groupId] = $groupId;
                }
            }
        }
        return $this->_coreData->jsonEncode($result);
    }

    /**
     * Get websites
     *
     * @return \Magento\Core\Model\Website[]
     */
    public function getWebsites()
    {
        return $this->_storeManager->getWebsites();
    }
}
