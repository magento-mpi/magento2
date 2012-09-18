<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API ACL Rules
 *
 * @method int getRoleId()
 * @method Mage_Api_Model_Rules setRoleId(int $value)
 * @method string getResourceId()
 * @method Mage_Api_Model_Rules setResourceId(string $value)
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Acl_Rule extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Resource_Acl_Rule');
    }
}
