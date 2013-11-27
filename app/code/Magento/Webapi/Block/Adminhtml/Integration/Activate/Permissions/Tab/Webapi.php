<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Block\Adminhtml\Integration\Activate\Permissions\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\View\Block\Template;
use Magento\Acl\Resource\ProviderInterface;
use Magento\Core\Helper\Data as CoreHelper;
use Magento\View\Block\Template\Context;
use Magento\Integration\Helper\Data as IntegrationHelper;

/**
 * API permissions tab for integration activation dialog.
 */
class Webapi extends Template implements TabInterface
{

    /** @var \Magento\Acl\Resource\ProviderInterface */
    protected $_resourceProvider;

    /** @var IntegrationHelper */
    protected $_integrationData;

    public function __construct(
        Context $context,
        CoreHelper $coreData,
        ProviderInterface $resourceProvider,
        IntegrationHelper $integrationData,
        array $data = array()
    ) {
        $this->_resourceProvider = $resourceProvider;
        $this->_integrationData = $integrationData;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function canShowTab()
    {
        return true;
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
     * Get requested permissions tree.
     *
     * @return string
     */
    public function getResourcesTree()
    {
        $resources = $this->_resourceProvider->getAclResources();
        $aclResourcesTree = $this->_integrationData->mapResources($resources[1]['children']);

        return $aclResourcesTree;
    }
}
