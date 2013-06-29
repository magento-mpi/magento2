<?php
/**
 * Default acl loader. Used as a fallback when no loaders were defined. Doesn't change ACL object passed.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Loader_Default implements Magento_Acl_LoaderInterface
{
    /**
     * Don't do anything to acl object.
     *
     * @param Magento_Acl $acl
     * @return mixed
     */
    public function populateAcl(Magento_Acl $acl)
    {
        // Do nothing
    }
}
