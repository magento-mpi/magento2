<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rolesedit Tab Display Block
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Block_Role_Tab_Edit extends Magento_Backend_Block_Widget_Form
    implements Magento_Backend_Block_Widget_Tab_Interface
{

    protected $_template = 'role/edit.phtml';

    /**
     * Root ACL Resource
     *
     * @var Magento_Core_Model_Acl_RootResource
     */
    protected $_rootResource;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Acl_RootResource $rootResource
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Acl_RootResource $rootResource,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_rootResource = $rootResource;
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Role Resources');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $rid = Mage::app()->getRequest()->getParam('rid', false);

        $acl = Mage::getSingleton('Magento_Acl_Builder')->getAcl();
        $rulesSet = Mage::getResourceModel('Magento_User_Model_Resource_Rules_Collection')->getByRoles($rid)->load();

        $selectedResourceIds = array();

        foreach ($rulesSet->getItems() as $item) {
            $itemResourceId = $item->getResource_id();
            if ($acl->has($itemResourceId) && $item->getPermission() == 'allow') {
                array_push($selectedResourceIds, $itemResourceId);
            }
        }

        $this->setSelectedResources($selectedResourceIds);
    }

    /**
     * Check if everything is allowed
     *
     * @return boolean
     */
    public function isEverythingAllowed()
    {
        return in_array($this->_rootResource->getId(), $this->getSelectedResources());
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return array
     */
    public function getTree()
    {
        /** @var $resourceProvider Magento_Acl_Resource_ProviderInterface */
        $resourceProvider = Mage::getSingleton('Magento_Acl_Resource_ProviderInterface');
        $resources = $resourceProvider->getAclResources();
        $rootArray = $this->_mapResources(
            isset($resources[1]['children']) ? $resources[1]['children'] : array()
        );
        return $rootArray;
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
            $item['attr']['data-id'] = $resource['id'];
            $item['data'] = __($resource['title']);
            $item['children'] = array();
            if (isset($resource['children'])) {
                $item['state'] = 'open';
                $item['children'] = $this->_mapResources($resource['children']);
            }
            $output[] = $item;
        }
        return $output;
    }
}
