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
 * @method Mage_Webapi_Model_Acl_Rule setRoleId(int $value)
 * @method string getResourceId()
 * @method Mage_Webapi_Model_Acl_Rule setResourceId(string $value)
 * @method Mage_Webapi_Model_Acl_Rule setResources(array $resources)
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Acl_Rule extends Mage_Core_Model_Abstract
{
    /**
     * Web API ACL config's resources root ID
     */
    const API_ACL_RESOURCES_ROOT_ID = 'Mage_Webapi';

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
