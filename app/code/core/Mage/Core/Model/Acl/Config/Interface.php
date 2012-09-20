<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Object that implements this interface should give ACL resource list.
 */
interface Mage_Core_Model_Acl_Config_Interface
{
    /**
     * Return ACL Resources loaded from anywhere
     *
     * @return DOMNodeList
     */
    public function getAclResources();

}
