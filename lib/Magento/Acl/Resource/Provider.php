<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Acl_Resource_Provider implements Magento_Acl_Resource_ProviderInterface
{
    /**
     * @var Magento_Config_ReaderInterface
     */
    protected $_configReader;

    /**
     * @var Magento_Config_ScopeInterface
     */
    protected $_scope;

    /**
     * @var Magento_Acl_Resource_TreeBuilder
     */
    protected $_resourceTreeBuilder;

    /**
     * @param Magento_Config_ReaderInterface $configReader
     * @param Magento_Config_ScopeInterface $scope
     * @param Magento_Acl_Resource_TreeBuilder $resourceTreeBuilder
     */
    public function __construct(
        Magento_Config_ReaderInterface $configReader,
        Magento_Config_ScopeInterface $scope,
        Magento_Acl_Resource_TreeBuilder $resourceTreeBuilder
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
