<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Block\Role\Tab;

/**
 * Rolesedit Tab Display Block.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Edit extends \Magento\Backend\Block\Widget\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'role/edit.phtml';

    /**
     * Root ACL Resource
     *
     * @var \Magento\Framework\Acl\RootResource
     */
    protected $_rootResource;

    /**
     * Rules collection factory
     *
     * @var \Magento\Authorization\Model\Resource\Rules\CollectionFactory
     */
    protected $_rulesCollectionFactory;

    /**
     * Acl builder
     *
     * @var \Magento\Framework\Acl\Builder
     */
    protected $_aclBuilder;

    /**
     * Acl resource provider
     *
     * @var \Magento\Framework\Acl\Resource\ProviderInterface
     */
    protected $_aclResourceProvider;

    /** @var \Magento\Integration\Helper\Data */
    protected $_integrationData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Acl\RootResource $rootResource
     * @param \Magento\Authorization\Model\Resource\Rules\CollectionFactory $rulesCollectionFactory
     * @param \Magento\Framework\Acl\Builder $aclBuilder
     * @param \Magento\Framework\Acl\Resource\ProviderInterface $aclResourceProvider
     * @param \Magento\Integration\Helper\Data $integrationData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Acl\RootResource $rootResource,
        \Magento\Authorization\Model\Resource\Rules\CollectionFactory $rulesCollectionFactory,
        \Magento\Framework\Acl\Builder $aclBuilder,
        \Magento\Framework\Acl\Resource\ProviderInterface $aclResourceProvider,
        \Magento\Integration\Helper\Data $integrationData,
        array $data = array()
    ) {
        $this->_aclBuilder = $aclBuilder;
        $this->_rootResource = $rootResource;
        $this->_rulesCollectionFactory = $rulesCollectionFactory;
        $this->_aclResourceProvider = $aclResourceProvider;
        $this->_integrationData = $integrationData;
        parent::__construct($context, $data);
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
     *
     * @return void
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
                $selectedResourceIds[] = $itemResourceId;
            }
        }

        $this->setSelectedResources($selectedResourceIds);
    }

    /**
     * Check if everything is allowed
     *
     * @return bool
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
        $rootArray = $this->_integrationData->mapResources(
            isset($resources[1]['children']) ? $resources[1]['children'] : array()
        );
        return $rootArray;
    }
}
