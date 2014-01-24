<?php
/**
 * ACL Resource Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl\Loader;

use Magento\Acl;
use Magento\Acl\Resource\ProviderInterface;
use Magento\Acl\ResourceFactory;

class Resource implements \Magento\Acl\LoaderInterface
{
    /**
     * Acl resource config
     *
     * @var ProviderInterface $resourceProvider
     */
    protected $_resourceProvider;

    /**
     * Resource factory
     *
     * @var ResourceFactory
     */
    protected $_resourceFactory;

    /**
     * @param ProviderInterface $resourceProvider
     * @param ResourceFactory $resourceFactory
     */
    public function __construct(
        ProviderInterface $resourceProvider,
        ResourceFactory $resourceFactory
    ) {
        $this->_resourceProvider = $resourceProvider;
        $this->_resourceFactory = $resourceFactory;
    }

    /**
     * Populate ACL with resources from external storage
     *
     * @param Acl $acl
     * @return void
     */
    public function populateAcl(Acl $acl)
    {
        $this->_addResourceTree($acl, $this->_resourceProvider->getAclResources(), null);
    }

    /**
     * Add list of nodes and their children to acl
     *
     * @param Acl $acl
     * @param array $resources
     * @param \Magento\Acl\Resource $parent
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function _addResourceTree(Acl $acl, array $resources, \Magento\Acl\Resource $parent = null)
    {
        foreach ($resources as $resourceConfig) {
            if (!isset($resourceConfig['id'])) {
                throw new \InvalidArgumentException('Missing ACL resource identifier');
            }
            /** @var $resource \Magento\Acl\Resource */
            $resource = $this->_resourceFactory->createResource(array('resourceId' => $resourceConfig['id']));
            $acl->addResource($resource, $parent);
            if (isset($resourceConfig['children'])) {
                $this->_addResourceTree($acl, $resourceConfig['children'], $resource);
            }
        }
    }
}
