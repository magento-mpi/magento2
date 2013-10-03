<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Acl\Resource;

class Provider implements \Magento\Acl\Resource\ProviderInterface
{
    /**
     * @var \Magento\Config\ReaderInterface
     */
    protected $_configReader;

    /**
     * @var \Magento\Config\ScopeInterface
     */
    protected $_scope;

    /**
     * @var \Magento\Acl\Resource\TreeBuilder
     */
    protected $_resourceTreeBuilder;

    /**
     * @param \Magento\Config\ReaderInterface $configReader
     * @param \Magento\Config\ScopeInterface $scope
     * @param \Magento\Acl\Resource\TreeBuilder $resourceTreeBuilder
     */
    public function __construct(
        \Magento\Config\ReaderInterface $configReader,
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
        $aclResourceConfig = $this->_configReader->read($this->_scope->getCurrentScope());
        if (!empty($aclResourceConfig['config']['acl']['resources'])) {
            return $this->_resourceTreeBuilder->build($aclResourceConfig['config']['acl']['resources']);
        }
        return array();
    }

}
