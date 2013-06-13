<?php
/**
 * Acl resources reader interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Acl_Loader_Resource_ConfigReaderInterface
{
    /**
     * Retrieve ACL resources
     *
     * @return array
     */
    public function getAclResources();
}
