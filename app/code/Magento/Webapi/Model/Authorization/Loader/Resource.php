<?php
/**
 * API ACL Resource Loader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_Webapi_Model_Authorization_Loader_Resource
 */
class Magento_Webapi_Model_Authorization_Loader_Resource extends Magento_Acl_Loader_Resource
{
    /**
     * @param Magento_Webapi_Model_Acl_Resource_ProviderInterface $resourceProvider
     * @param Magento_Acl_ResourceFactory $resourceFactory
     */
    public function __construct(
        Magento_Webapi_Model_Acl_Resource_ProviderInterface $resourceProvider,
        Magento_Acl_ResourceFactory $resourceFactory
    ) {
        parent::__construct($resourceProvider, $resourceFactory);
    }

    /**
     * Deny each resource for all roles.
     *
     * @param Magento_Acl $acl
     */
    protected function _denyResources(Magento_Acl $acl)
    {
        foreach ($acl->getResources() as $aclResource) {
            $acl->deny(null, $aclResource);
        }
    }

    /**
     * Load virtual resources as sub-resources of existing one.
     *
     * @param Magento_Acl $acl
     */
    protected function _loadVirtualResources(Magento_Acl $acl)
    {
        $virtualResources = $this->_resourceProvider->getAclVirtualResources();
        foreach ($virtualResources as $virtualResource) {
            $resourceParent = $virtualResource['parent'];
            $resourceId = $virtualResource['id'];
            if ($acl->has($resourceParent) && !$acl->has($resourceId)) {
                /** @var $resource Magento_Acl_Resource */
                $resource = $this->_resourceFactory->createResource(array('resourceId' => $resourceId));
                $acl->addResource($resource, $resourceParent);
            }
        }
    }

    /**
     * Populate ACL with resources from external storage.
     *
     * @param Magento_Acl $acl
     * @throws Magento_Core_Exception
     */
    public function populateAcl(Magento_Acl $acl)
    {
        parent::populateAcl($acl);
        $this->_denyResources($acl);
        $this->_loadVirtualResources($acl);
    }
}
