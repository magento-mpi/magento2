<?php
/**
 * Web API Role Resource Collection.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Resource_Acl_Role_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization.
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Acl_Role', 'Mage_Webapi_Model_Resource_Acl_Role');
    }
}
