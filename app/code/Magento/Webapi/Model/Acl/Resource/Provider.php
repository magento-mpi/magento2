<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Webapi_Model_Acl_Resource_Provider extends  \Magento\Acl\Resource\Provider
    implements Magento_Webapi_Model_Acl_Resource_ProviderInterface
{
    /**
     * @param Magento_Webapi_Model_Acl_Resource_Config_Reader_Filesystem $configReader
     * @param \Magento\Config\ScopeInterface $scope
     * @param \Magento\Acl\Resource\TreeBuilder $resourceTreeBuilder
     */
    public function __construct(
        Magento_Webapi_Model_Acl_Resource_Config_Reader_Filesystem $configReader,
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
