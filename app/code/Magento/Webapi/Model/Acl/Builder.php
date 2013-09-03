<?php
/**
 * WebAPI ACL Builder
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Builder extends \Magento\Acl\Builder
{
    /**
     * @param \Magento\AclFactory $aclFactory
     * @param \Magento\Acl\CacheInterface $cache
     * @param \Magento\Acl\LoaderInterface $roleLoader
     * @param \Magento\Acl\LoaderInterface $resourceLoader
     * @param \Magento\Acl\LoaderInterface $ruleLoader
     */
    public function __construct(
        \Magento\AclFactory $aclFactory,
        \Magento\Acl\CacheInterface $cache,
        \Magento\Acl\LoaderInterface $roleLoader,
        \Magento\Acl\LoaderInterface $resourceLoader,
        \Magento\Acl\LoaderInterface $ruleLoader
    ) {
        parent::__construct($aclFactory, $cache, $roleLoader, $resourceLoader, $ruleLoader);
    }
}
