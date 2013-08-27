<?php
/**
 * Web API Role Resource Collection.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Resource_Acl_Role_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization.
     */
    protected function _construct()
    {
        $this->_init('Magento_Webapi_Model_Acl_Role', 'Magento_Webapi_Model_Resource_Acl_Role');
    }
}
