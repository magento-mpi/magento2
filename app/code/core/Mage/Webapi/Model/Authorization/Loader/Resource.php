<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Api Acl Resource Loader
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Authorization_Loader_Resource extends Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract
{

    /**
     * @param Mage_Webapi_Model_Authorization_Config $config
     * @param Magento_Acl_ResourceFactory $resourceFactory
     */
    public function __construct(Mage_Webapi_Model_Authorization_Config $config,
        Magento_Acl_ResourceFactory $resourceFactory
    ) {
        $this->_config = $config;
        $this->_resourceFactory = $resourceFactory;
    }

    /**
     * Deny each resource for all roles
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
     * Load virtual resources as sub-resources of existing
     *
     * @param Magento_Acl $acl
     */
    protected function _loadVirtualResources(Magento_Acl $acl)
    {
        $virtualResources = $this->_config->getAclVirtualResources();
        /** @var $resourceConfig DOMElement */
        foreach ($virtualResources as $resourceConfig) {
            if (!($resourceConfig instanceof DOMElement)) {
                continue;
            }
            $parent = $resourceConfig->getAttribute('parent');
            $resourceId = $resourceConfig->getAttribute('id');
            if ($acl->has($parent) && !$acl->has($resourceId)) {
                /** @var $resource Magento_Acl_Resource */
                $resource = $this->_resourceFactory->createResource(array($resourceId));
                $acl->addResource($resource, $parent);
            }
        }
    }

    /**
     * Populate ACL with resources from external storage
     *
     * @param Magento_Acl $acl
     * @throws Mage_Core_Exception
     */
    public function populateAcl(Magento_Acl $acl)
    {
        if (!($this->_config instanceof Mage_Webapi_Model_Authorization_Config)) {
            throw new Mage_Core_Exception('Config loader is not correct for the resource loader');
        }
        parent::populateAcl($acl);
        $this->_denyResources($acl);
        $this->_loadVirtualResources($acl);
    }
}
