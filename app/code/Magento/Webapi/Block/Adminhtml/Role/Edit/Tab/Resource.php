<?php
/**
 * Web API role resource tab.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource setApiRole(\Magento\Webapi\Model\Acl\Role $role)
 * @method \Magento\Webapi\Model\Acl\Role getApiRole() getApiRole()
 * @method \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource setSelectedResources(array $srIds)
 * @method array getSelectedResources() getSelectedResources()
 */
namespace Magento\Webapi\Block\Adminhtml\Role\Edit\Tab;

class Resource extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Web API ACL resources tree root ID.
     */
    const RESOURCES_TREE_ROOT_ID = '__root__';

    /**
     * @var Magento_Acl_Resource_ProviderInterface
     */
    protected $_resourceProvider;

    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Rule
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
     * Root ACL Resource
     *
     * @var \Magento\Core\Model\Acl\RootResource
     */
    protected $_rootResource;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Acl\Resource\ProviderInterface $resourceProvider
     * @param \Magento\Webapi\Model\Resource\Acl\Rule $ruleResource
     * @param \Magento\Core\Model\Acl\RootResource $rootResource
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Acl\Resource\ProviderInterface $resourceProvider,
        \Magento\Webapi\Model\Resource\Acl\Rule $ruleResource,
        \Magento\Core\Model\Acl\RootResource $rootResource,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_resourceProvider = $resourceProvider;
        $this->_ruleResource = $ruleResource;
        $this->_rootResource = $rootResource;
    }

    /**
     * Prepare Form.
     *
     * @return \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource
     */
    protected function _prepareForm()
    {
        $resources = $this->_resourceProvider->getAclResources();
        $this->_aclResourcesTree = $this->_mapResources($resources[1]['children']);
        return parent::_prepareForm();
    }

    /**
     * Map resources
     *
     * @param array $resources
     * @return array
     */
    protected function _mapResources(array $resources)
    {
        $output = array();
        foreach ($resources as $resource) {
            $item = array();
            $item['id'] = $resource['id'];
            $item['text'] = __($resource['title']);
            if (in_array($item['id'], $this->_getSelectedResourcesIds())) {
                $item['checked'] = true;
            }
            $item['children'] = array();
            if (isset($resource['children'])) {
                $item['children'] = $this->_mapResources($resource['children']);
            }
            $output[] = $item;
        }
        return $output;
    }

    /**
     * Check whether resource access is set to "All".
     *
     * @return bool
     */
    public function isEverythingAllowed()
    {
        return in_array($this->_rootResource->getId(), $this->_getSelectedResourcesIds());
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
