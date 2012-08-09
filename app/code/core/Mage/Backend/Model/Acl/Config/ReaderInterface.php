<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Acl resources reader interface
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Backend_Model_Acl_Config_ReaderInterface
{
    /**
     * Retrieve ACL resources
     * @abstract
     * @return mixed
     */
    function getAclResources();
}
