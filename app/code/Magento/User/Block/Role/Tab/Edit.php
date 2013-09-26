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
namespace Magento\User\Block\Role\Tab;

class Edit extends \Magento\Backend\Block\Widget\Form
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_template = 'role/edit.phtml';

    /**
     * Root ACL Resource
     *
     * @var \Magento\Core\Model\Acl\RootResource
     */
    protected $_rootResource;

    /**
     * Rules collection factory
     *
     * @var \Magento\User\Model\Resource\Rules\CollectionFactory
     */
    protected $_rulesCollectionFactory;

    /**
     * Acl builder
     *
     * @var \Magento\Acl\Builder
     */
    protected $_aclBuilder;

    /**
     * Acl resource provider
     *
     * @var \Magento\Acl\Resource\ProviderInterface
     */
    protected $_aclResourceProvider;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Acl\RootResource $rootResource
     * @param \Magento\User\Model\Resource\Rules\CollectionFactory $rulesCollectionFactory
     * @param \Magento\Acl\Builder $aclBuilder
     * @param \Magento\Acl\Resource\ProviderInterface $aclResourceProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Acl\RootResource $rootResource,
        \Magento\User\Model\Resource\Rules\CollectionFactory $rulesCollectionFactory,
        \Magento\Acl\Builder $aclBuilder,
        \Magento\Acl\Resource\ProviderInterface $aclResourceProvider,
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
