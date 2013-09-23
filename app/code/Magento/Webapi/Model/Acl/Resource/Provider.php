<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Webapi_Model_Acl_Resource_Provider implements Magento_Webapi_Model_Acl_Resource_ProviderInterface
{
    /**
     * @var Magento_Webapi_Model_Acl_Resource_Config_Reader_Filesystem
     */
    protected $_configReader;

    /**
     * @var Magento_Acl_Resource_TreeBuilder
     */
    protected $_resourceTreeBuilder;

    /**
     * @var Magento_Config_ScopeInterface
     */
    protected $_scope;

    /**
     * @param Magento_Webapi_Model_Acl_Resource_Config_Reader_Filesystem $configReader
     * @param Magento_Config_ScopeInterface $scope
     * @param Magento_Acl_Resource_TreeBuilder $resourceTreeBuilder
     */
    public function __construct(
        Magento_Webapi_Model_Acl_Resource_Config_Reader_Filesystem $configReader,
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
