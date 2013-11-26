<?php
/**
 * API permissions tab for integration activation dialog.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Integration\Activate\Permissions\Tabs;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\View\Block\Template;
use Magento\Acl\Resource\ProviderInterface;
use Magento\Core\Helper\Data as CoreHelper;
use Magento\View\Block\Template\Context;

class Webapi extends Template implements TabInterface
{
    /**
     * @var \Magento\Acl\Resource\ProviderInterface
     */
    protected $_resourceProvider;

    public function __construct(
        Context $context,
        CoreHelper $coreData,
        ProviderInterface $resourceProvider,
        array $data = array()
    ) {
        $this->_resourceProvider = $resourceProvider;
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
        $aclResourcesTree = $this->_mapResources($resources[1]['children']);

        return $aclResourcesTree;
    }

    /**
     * Make ACL resource array compatible with jsTree component.
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
