<?php
/**
 * Role item model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Acl_Role extends Mage_Core_Model_Abstract
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
        $this->_init('Mage_Webapi_Model_Resource_Acl_Role');
    }
}
