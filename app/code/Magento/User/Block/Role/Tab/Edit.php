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
     * @var \Magento\Acl\Builder
     */
    protected $_aclBuilder;

    /**
     * @var \Magento\User\Model\Resource\Rules\CollectionFactory
     */
    protected $_userRulesFactory;

    /**
     * @var \Magento\Acl\Resource\Provider
     */
    protected $_aclProvider;

    /**
     * @param \Magento\Acl\Builder $aclBuilder
     * @param \Magento\Acl\Resource\ProviderInterface $aclProvider
     * @param \Magento\User\Model\Resource\Rules\CollectionFactory $userRulesFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Acl\RootResource $rootResource
     * @param array $data
     */
    public function __construct(
        \Magento\Acl\Builder $aclBuilder,
        \Magento\Acl\Resource\ProviderInterface $aclProvider,
        \Magento\User\Model\Resource\Rules\CollectionFactory $userRulesFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Acl\RootResource $rootResource,
        array $data = array()
    ) {
        $this->_aclBuilder = $aclBuilder;
        $this->_aclProvider = $aclProvider;
        $this->_userRulesFactory = $userRulesFactory;
        parent::__construct($coreData, $context, $data);
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

        $rid = \Mage::app()->getRequest()->getParam('rid', false);

        $acl = $this->_aclBuilder->getAcl();
        $rulesSet = $this->_userRulesFactory->create()->getByRoles($rid)->load();

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
        $resources = $this->_aclProvider->getAclResources();
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
