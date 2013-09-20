<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rolesedit Tab Display Block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
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
     * Rules collection factory
     *
     * @var Magento_User_Model_Resource_Rules_CollectionFactory
     */
    protected $_rulesCollectionFactory;

    /**
     * Acl builder
     *
     * @var Magento_Acl_Builder
     */
    protected $_aclBuilder;

    /**
     * Acl resource provider
     *
     * @var Magento_Acl_Resource_ProviderInterface
     */
    protected $_aclResourceProvider;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Acl_RootResource $rootResource
     * @param Magento_User_Model_Resource_Rules_CollectionFactory $rulesCollectionFactory
     * @param Magento_Acl_Builder $aclBuilder
     * @param Magento_Acl_Resource_ProviderInterface $aclResourceProvider
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Acl_RootResource $rootResource,
        Magento_User_Model_Resource_Rules_CollectionFactory $rulesCollectionFactory,
        Magento_Acl_Builder $aclBuilder,
        Magento_Acl_Resource_ProviderInterface $aclResourceProvider,
        array $data = array()
    ) {
        $this->_aclBuilder = $aclBuilder;
        $this->_rootResource = $rootResource;
        $this->_rulesCollectionFactory = $rulesCollectionFactory;
        $this->_aclResourceProvider = $aclResourceProvider;
        parent::__construct($coreData, $context, $data);
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

        $rid = $this->_request->getParam('rid', false);

        $acl = $this->_aclBuilder->getAcl();
        $rulesSet = $this->_rulesCollectionFactory->create()->getByRoles($rid)->load();

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
        $resources = $this->_aclResourceProvider->getAclResources();
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
