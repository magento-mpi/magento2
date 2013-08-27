<?php
/**
 * Web API ACL Rules.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method int getRoleId() getRoleId()
 * @method Magento_Webapi_Model_Acl_Rule setRoleId() setRoleId(int $value)
 * @method string getResourceId() getResourceId()
 * @method Magento_Webapi_Model_Resource_Acl_Rule getResource() getResource()
 * @method Magento_Webapi_Model_Resource_Acl_Rule_Collection getCollection() getCollection()
 * @method Magento_Webapi_Model_Acl_Rule setResourceId() setResourceId(string $value)
 * @method Magento_Webapi_Model_Acl_Rule setResources() setResources(array $resources)
 * @method array getResources() getResources()
 */
class Magento_Webapi_Model_Acl_Rule extends Magento_Core_Model_Abstract
{
    /**
     * Web API ACL resource separator.
     */
    const RESOURCE_SEPARATOR = '/';

    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_init('Magento_Webapi_Model_Resource_Acl_Rule');
    }

    /**
     * Save role resources.
     *
     * @return Magento_Webapi_Model_Acl_Rule
     */
    public function saveResources()
    {
        $this->getResource()->saveResources($this);
        return $this;
    }
}
