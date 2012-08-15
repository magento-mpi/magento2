<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Acl
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default acl loader. Used as a fallback when no loaders were defined. Doesn't change ACL object passed.
 */
class Magento_Acl_Loader_Default implements Magento_Acl_Loader
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
