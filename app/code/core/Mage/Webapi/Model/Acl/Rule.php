<?php
/**
 * Web API ACL Rules
 *
 * @copyright {}
 *
 * @method int getRoleId()
 * @method Mage_Webapi_Model_Acl_Rule setRoleId(int $value)
 * @method string getResourceId()
 * @method Mage_Webapi_Model_Resource_Acl_Rule getResource()
 * @method Mage_Webapi_Model_Resource_Acl_Rule_Collection getCollection()
 * @method Mage_Webapi_Model_Acl_Rule setResourceId(string $value)
 * @method Mage_Webapi_Model_Acl_Rule setResources() setResources(array $resources)
 * @method array getResources()
 */
class Mage_Webapi_Model_Acl_Rule extends Mage_Core_Model_Abstract
{
    /**
     * Web API ACL config's resources root ID
     */
    const API_ACL_RESOURCES_ROOT_ID = 'Mage_Webapi';

    /**
     * Web API ACL resource separator
     */
    const RESOURCE_SEPARATOR = '/';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Resource_Acl_Rule');
    }

    /**
     * Save role resources
     *
     * @return Mage_Webapi_Model_Acl_Rule
     */
    public function saveResources()
    {
        $this->getResource()->saveResources($this);
        return $this;
    }
}
