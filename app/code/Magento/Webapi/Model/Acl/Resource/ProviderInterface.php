<?php
/**
 * Web API ACL resources provider interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\Resource;

interface ProviderInterface extends \Magento\Acl\Resource\ProviderInterface
{
    /**
     * Retrieve ACL Virtual Resources.
     *
     * Virtual resources are not shown in resource list, they use existing resource to check permission.
     *
     * @return array
     */
    public function getAclVirtualResources();
}

