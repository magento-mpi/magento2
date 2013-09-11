<?php
/**
 * API ACL Resource Loader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization\Loader;

class Resource extends \Magento\Acl\Loader\Resource
{
    /**
     * @param \Magento\Webapi\Model\Acl\Resource\ProviderInterface $resourceProvider
     * @param \Magento\Acl\ResourceFactory $resourceFactory
     */
    public function __construct(
        \Magento\Webapi\Model\Acl\Resource\ProviderInterface $resourceProvider,
        \Magento\Acl\ResourceFactory $resourceFactory
    ) {
        parent::__construct($resourceProvider, $resourceFactory);
    }

    /**
     * Deny each resource for all roles.
     *
     * @param \Magento\Acl $acl
     */
    protected function _denyResources(\Magento\Acl $acl)
    {
        foreach ($acl->getResources() as $aclResource) {
            $acl->deny(null, $aclResource);
        }
    }

    /**
     * Load virtual resources as sub-resources of existing one.
     *
     * @param \Magento\Acl $acl
     */
    protected function _loadVirtualResources(\Magento\Acl $acl)
    {
        $virtualResources = $this->_resourceProvider->getAclVirtualResources();
        foreach ($virtualResources as $virtualResource) {
            $resourceParent = $virtualResource['parent'];
            $resourceId = $virtualResource['id'];
            if ($acl->has($resourceParent) && !$acl->has($resourceId)) {
                /** @var $resource \Magento\Acl\Resource */
                $resource = $this->_resourceFactory->createResource(array('resourceId' => $resourceId));
                $acl->addResource($resource, $resourceParent);
            }
        }
    }

    /**
     * Populate ACL with resources from external storage.
     *
     * @param \Magento\Acl $acl
     * @throws \Magento\Core\Exception
     */
    public function populateAcl(\Magento\Acl $acl)
    {
        parent::populateAcl($acl);
        $this->_denyResources($acl);
        $this->_loadVirtualResources($acl);
    }
}
