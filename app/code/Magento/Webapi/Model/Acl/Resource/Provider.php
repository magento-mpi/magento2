<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Webapi\Model\Acl\Resource;

class Provider extends  \Magento\Acl\Resource\Provider
    implements \Magento\Webapi\Model\Acl\Resource\ProviderInterface
{
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
        parent::__construct($configReader, $scope, $resourceTreeBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getAclVirtualResources()
    {
        $aclResourceConfig = $this->_configReader->read($this->_scope->getCurrentScope());
        return $aclResourceConfig['config']['mapping'];
    }
}
