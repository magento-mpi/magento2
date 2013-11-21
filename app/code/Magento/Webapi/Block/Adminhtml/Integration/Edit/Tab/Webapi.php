<?php
/**
 * Main Web API properties edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Block\Adminhtml\Integration\Edit\Tab;

use Magento\Integration\Controller\Adminhtml\Integration as IntegrationController;

/**
 * Class for handling API section within integration
 */
class Webapi extends \Magento\Backend\Block\Widget\Form
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
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
    protected $_rulesCollFactory;

    /**
     * Acl resource provider
     *
     * @var \Magento\Acl\Resource\ProviderInterface
     */
    protected $_aclResourceProvider;

    /** @var \Magento\Core\Model\Registry */
    protected $_registry;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Acl\RootResource $rootResource
     * @param \Magento\User\Model\Resource\Rules\CollectionFactory $rulesCollFactory
     * @param \Magento\Acl\Resource\ProviderInterface $aclResourceProvider
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Acl\RootResource $rootResource,
        \Magento\User\Model\Resource\Rules\CollectionFactory $rulesCollFactory,
        \Magento\Acl\Resource\ProviderInterface $aclResourceProvider,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_rootResource = $rootResource;
        $this->_rulesCollFactory = $rulesCollFactory;
        $this->_aclResourceProvider = $aclResourceProvider;
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('API');
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
        $selectedResourceIds = array();
        $currentIntegration = $this->_registry->registry(IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION);
        if ($currentIntegration
            && isset($currentIntegration['resource']) && is_array($currentIntegration['resource'])
        ) {
            $selectedResourceIds = $currentIntegration['resource'];
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
            $item['data'] = $resource['title'];
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
