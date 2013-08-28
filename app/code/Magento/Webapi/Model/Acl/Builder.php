<?php
/**
 * WebAPI ACL Builder
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Builder extends Magento_Acl_Builder
{
    /**
     * @param Magento_AclFactory $aclFactory
     * @param Magento_Acl_CacheInterface $cache
     * @param Magento_Acl_LoaderInterface $roleLoader
     * @param Magento_Acl_LoaderInterface $resourceLoader
     * @param Magento_Acl_LoaderInterface $ruleLoader
     */
    public function __construct(
        Magento_AclFactory $aclFactory,
        Magento_Acl_CacheInterface $cache,
        Magento_Acl_LoaderInterface $roleLoader,
        Magento_Acl_LoaderInterface $resourceLoader,
        Magento_Acl_LoaderInterface $ruleLoader
    ) {
        parent::__construct($aclFactory, $cache, $roleLoader, $resourceLoader, $ruleLoader);
    }
}
