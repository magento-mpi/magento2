<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Block\Adminhtml\Integration\Activate\Permissions\Tab;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Controller\Adminhtml\Integration as IntegrationController;
use Magento\Integration\Model\Integration as IntegrationModel;
use Magento\Webapi\Helper\Data as WebapiHelper;

/**
 * API permissions tab for integration activation dialog.
 */
class Webapi extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /** @var string[] */
    protected $_selectedResources;

    /** @var \Magento\Core\Model\Acl\RootResource */
    protected $_rootResource;

    /** @var \Magento\Acl\Resource\ProviderInterface */
    protected $_resourceProvider;

    /** @var \Magento\Integration\Helper\Data */
    protected $_integrationData;

    /** @var WebapiHelper */
    protected $_webapiHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Acl\RootResource $rootResource
     * @param \Magento\Acl\Resource\ProviderInterface $resourceProvider
     * @param \Magento\Integration\Helper\Data $integrationData
     * @param \Magento\Webapi\Helper\Data $webapiData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Acl\RootResource $rootResource,
        \Magento\Acl\Resource\ProviderInterface $resourceProvider,
        \Magento\Integration\Helper\Data $integrationData,
        \Magento\Webapi\Helper\Data $webapiData,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        $this->_rootResource = $rootResource;
        $this->_resourceProvider = $resourceProvider;
        $this->_integrationData = $integrationData;
        $this->_webapiHelper = $webapiData;
        parent::__construct($context, $coreData, $registry, $formFactory, $data);
    }

    /**
     * Set the selected resources, which is an array of resource ids. If everything is allowed, the
     * array will contain just the root resource id, which is "Magento_Adminhtml::all".
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_selectedResources = $this->_webapiHelper->getSelectedResources();
    }

    /**
     * {@inheritDoc}
     */
    public function canShowTab()
    {
        $integrationData = $this->_coreRegistry->registry(IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION);
        return $integrationData[Info::DATA_SETUP_TYPE] == IntegrationModel::TYPE_CONFIG;
    }

    /**
     * {@inheritDoc}
     */
    public function getTabLabel()
    {
        return __('API');
    }

    /**
     * {@inheritDoc}
     */
    public function getTabTitle()
    {
        return __('API');
    }

    /**
     * {@inheritDoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check if everything is allowed.
     *
     * @return bool
     */
    public function isEverythingAllowed()
    {
        return in_array($this->_rootResource->getId(), $this->_selectedResources);
    }

    /**
     * Get requested permissions tree.
     *
     * @return string
     */
    public function getResourcesTreeJson()
    {
        $resources = $this->_resourceProvider->getAclResources();
        $aclResourcesTree = $this->_integrationData->mapResources($resources[1]['children']);

        return $this->_coreData->jsonEncode($aclResourcesTree);
    }

    /**
     * Return an array of selected resource ids. If everything is allowed then iterate through all
     * available resources to generate a comprehensive array of all resource ids, rather than just
     * returning "Magento_Adminhtml::all".
     *
     * @return string
     */
    public function getSelectedResourcesJson()
    {
        $selectedResources = $this->_selectedResources;
        if ($this->isEverythingAllowed()) {
             $resources = $this->_resourceProvider->getAclResources();
             $selectedResources = $this->_getAllResourceIds($resources[1]['children']);
        }
        return $this->_coreData->jsonEncode($selectedResources);
    }

    /**
     * Return an array of all resource Ids.
     *
     * @param array $resources
     * @return string[]
     */
    protected function _getAllResourceIds(array $resources)
    {
        $resourceIds = array();
        foreach ($resources as $resource) {
            $resourceIds[] = $resource['id'];
            if (isset($resource['children'])) {
                $resourceIds = array_merge($resourceIds, $this->_getAllResourceIds($resource['children']));
            }
        }
        return $resourceIds;
    }
}
