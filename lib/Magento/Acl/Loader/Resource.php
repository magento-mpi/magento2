<?php
/**
 * ACL Resource Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Loader_Resource implements Magento_Acl_LoaderInterface
{
    /**
     * Acl resource config
     *
     * @var Magento_Acl_Resource_ProviderInterface $resourceProvider
     */
    protected $_resourceProvider;

    /**
     * Resource factory
     *
     * @var Magento_Acl_ResourceFactory
     */
    protected $_resourceFactory;

    /**
     * @param Magento_Acl_Resource_ProviderInterface $resourceProvider
     * @param Magento_Acl_ResourceFactory $resourceFactory
     */
    public function __construct(
        Magento_Acl_Resource_ProviderInterface $resourceProvider,
        Magento_Acl_ResourceFactory $resourceFactory
    ) {
        $this->_resourceProvider = $resourceProvider;
        $this->_resourceFactory = $resourceFactory;
    }

    /**
     * Populate ACL with resources from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $this->_addResourceTree($acl, $this->_resourceProvider->getAclResources(), null);
    }

    /**
     * Add list of nodes and their children to acl
     *
     * @param Magento_Acl $acl
     * @param array $resources
     * @param Magento_Acl_Resource $parent
     * @throws InvalidArgumentException
     */
    protected function _addResourceTree(Magento_Acl $acl, array $resources, Magento_Acl_Resource $parent = null)
    {
        foreach ($resources as $resourceConfig) {
            if (!isset($resourceConfig['id'])) {
                throw new InvalidArgumentException('Missing ACL resource identifier');
            }
            /** @var $resource Magento_Acl_Resource */
            $resource = $this->_resourceFactory->createResource(array('resourceId' => $resourceConfig['id']));
            $acl->addResource($resource, $parent);
            if (isset($resourceConfig['children'])) {
                $this->_addResourceTree($acl, $resourceConfig['children'], $resource);
            }
        }
    }
}
