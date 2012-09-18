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
 * Web API Role source model
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Source_Acl_Role
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Role
     */
    protected $_resource = null;

    /**
     * Retrieve option hash of Web API Roles
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionHash($addEmpty = true)
    {
        $options = $this->_getResourceModel()->getRolesList();
        if ($addEmpty) {
            $options = array('' => '') + $options;
        }
        return $options;
    }

    /**
     * Get roles resource model
     *
     * @return Mage_Webapi_Model_Resource_Acl_Role
     */
    protected function _getResourceModel()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Role');
        }
        return $this->_resource;
    }
}
