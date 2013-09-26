<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Webapi\Model\Acl\Resource;

class Provider implements \Magento\Webapi\Model\Acl\Resource\ProviderInterface
{
    /**
     * @var \Magento\Webapi\Model\Acl\Resource\Config\Reader\Filesystem
     */
    protected $_configReader;

    /**
     * @var \Magento\Acl\Resource\TreeBuilder
     */
    protected $_resourceTreeBuilder;

    /**
     * @var \Magento\Config\ScopeInterface
     */
    protected $_scope;

    /**
     * @param \Magento\Webapi\Model\Acl\Resource\Config\Reader\Filesystem $configReader
     * @param \Magento\Config\ScopeInterface $scope
     * @param \Magento\Acl\Resource\TreeBuilder $resourceTreeBuilder
     */
    public function __construct(
        \Magento\Webapi\Model\Acl\Resource\Config\Reader\Filesystem $configReader,
        \Magento\Config\ScopeInterface $scope,
        \Magento\Acl\Resource\TreeBuilder $resourceTreeBuilder
    ) {
        $this->_configReader = $configReader;
        $this->_scope = $scope;
        $this->_resourceTreeBuilder = $resourceTreeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclResources()
    {
        $aclResourceConfig = $this->_configReader->read();
        if (!empty($aclResourceConfig['config']['acl']['resources'])) {
            return $this->_resourceTreeBuilder->build($aclResourceConfig['config']['acl']['resources']);
        }
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAclVirtualResources()
    {
        $aclResourceConfig = $this->_configReader->read();
        return isset($aclResourceConfig['config']['mapping']) ? $aclResourceConfig['config']['mapping'] : array();
    }
}
