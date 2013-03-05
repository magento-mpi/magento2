<?php
/**
 * Web API role resource tab.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource setApiRole() setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole() getApiRole()
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource setSelectedResources() setSelectedResources(array $srIds)
 * @method array getSelectedResources() getSelectedResources()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource extends Mage_Backend_Block_Widget_Form
{
    /**
     * @var Mage_Webapi_Model_Authorization_Config
     */
    protected $_authorizationConfig;

    /**
     * @var Mage_Webapi_Model_Resource_Acl_Rule
     */
    protected $_ruleResource;

    /**
     * @var array
     */
    protected $_aclResourcesTree;

    /**
     * @var array
     */
    protected $_selResourcesIds;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Webapi_Model_Authorization_Config $authorizationConfig
     * @param Mage_Webapi_Model_Resource_Acl_Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Webapi_Model_Authorization_Config $authorizationConfig,
        Mage_Webapi_Model_Resource_Acl_Rule $ruleResource,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_authorizationConfig = $authorizationConfig;
        $this->_ruleResource = $ruleResource;
    }

    /**
     * Prepare Form.
     *
     * @return Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource
     */
    protected function _prepareForm()
    {
        $this->_aclResourcesTree = $this->_authorizationConfig->getAclResourcesAsArray(false);
        $selectedResources = $this->_getSelectedResourcesIds();

        if ($selectedResources) {
            $selResourcesCallback = function (&$resourceItem) use ($selectedResources, &$selResourcesCallback) {
                if (in_array($resourceItem['id'], $selectedResources)) {
                    $resourceItem['checked'] = true;
                }
                if (!empty($resourceItem['children'])) {
                    array_walk($resourceItem['children'], $selResourcesCallback);
                }
            };
            array_walk($this->_aclResourcesTree, $selResourcesCallback);
        }

        return parent::_prepareForm();
    }

    /**
     * Check whether resource access is set to "All".
     *
     * @return bool
     */
    public function isEverythingAllowed()
    {
        return in_array(Mage_Webapi_Model_Authorization::API_ACL_RESOURCES_ROOT_ID, $this->_getSelectedResourcesIds());
    }

    /**
     * Get ACL resources tree.
     *
     * @return string
     */
    public function getResourcesTree()
    {
        return $this->_aclResourcesTree;
    }

    /**
     * Get selected ACL resources of given API role.
     *
     * @return array
     */
    protected function _getSelectedResourcesIds()
    {
        $apiRole = $this->getApiRole();
        if (null === $this->_selResourcesIds && $apiRole && $apiRole->getId()) {
            $this->_selResourcesIds = $this->_ruleResource->getResourceIdsByRole($apiRole->getRoleId());
        }
        return (array)$this->_selResourcesIds;
    }
}
