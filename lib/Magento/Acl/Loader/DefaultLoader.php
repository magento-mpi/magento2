<?php
/**
 * Default acl loader. Used as a fallback when no loaders were defined. Doesn't change ACL object passed.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl\Loader;

class DefaultLoader implements \Magento\Acl\LoaderInterface
{
    /**
     * Don't do anything to acl object.
     *
     * @param \Magento\Acl $acl
     * @return mixed
     */
    public function populateAcl(\Magento\Acl $acl)
    {
        // Do nothing
    }
}
