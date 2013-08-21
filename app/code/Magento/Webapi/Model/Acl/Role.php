<?php
/**
 * Role item model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Role extends Magento_Core_Model_Abstract
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'webapi_role';

    /**
     * Initialize resource.
     */
    protected function _construct()
    {
        $this->_init('Magento_Webapi_Model_Resource_Acl_Role');
    }
}
