<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Magento_Api_Model_Resource_Rules _getResource()
 * @method Magento_Api_Model_Resource_Rules getResource()
 * @method int getRoleId()
 * @method Magento_Api_Model_Rules setRoleId(int $value)
 * @method string getResourceId()
 * @method Magento_Api_Model_Rules setResourceId(string $value)
 * @method string getPrivileges()
 * @method Magento_Api_Model_Rules setPrivileges(string $value)
 * @method int getAssertId()
 * @method Magento_Api_Model_Rules setAssertId(int $value)
 * @method string getRoleType()
 * @method Magento_Api_Model_Rules setRoleType(string $value)
 * @method string getPermission()
 * @method Magento_Api_Model_Rules setPermission(string $value)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Rules extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Api_Model_Resource_Rules');
    }

    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    public function getCollection()
    {
        return Mage::getResourceModel('Magento_Api_Model_Resource_Permissions_Collection');
    }

    public function saveRel()
    {
        $this->getResource()->saveRel($this);
        return $this;
    }
}
