<?php
/**
 * API ACL Resource Loader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization_Loader_Resource extends Magento_Acl_Loader_Resource
{
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
        $virtualResources = $this->_configReader->getAclVirtualResources();
        /** @var $resourceConfig DOMElement */
        foreach ($virtualResources as $resourceConfig) {
            if (!($resourceConfig instanceof DOMElement)) {
                continue;
            }
            $parent = $resourceConfig->getAttribute('parent');
            $resourceId = $resourceConfig->getAttribute('id');
            if ($acl->has($parent) && !$acl->has($resourceId)) {
                /** @var $resource Magento_Acl_Resource */
                $resource = $this->_resourceFactory->createResource(array('resourceId' => $resourceId));
                $acl->addResource($resource, $parent);
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
