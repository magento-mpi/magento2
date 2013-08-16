<?php
/**
 * Acl resources provider interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Acl_Resource_ProviderInterface
{
    /**
     * Retrieve ACL resources
     *
     * @return array
     */
    public function getAclResources();
}

