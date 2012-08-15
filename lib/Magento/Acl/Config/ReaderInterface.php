<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  ACL
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Acl resources reader interface
 *
 * @category    Magento
 * @package     Framework
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Acl_Config_ReaderInterface
{
    /**
     * Retrieve ACL resources
     * @abstract
     * @return mixed
     */
    function getAclResources();
}
