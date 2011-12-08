<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Admin
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Rules Model
 *
 * @method Mage_Admin_Model_Resource_Rules _getResource()
 * @method Mage_Admin_Model_Resource_Rules getResource()
 * @method int getRoleId()
 * @method Mage_Admin_Model_Rules setRoleId(int $value)
 * @method string getResourceId()
 * @method Mage_Admin_Model_Rules setResourceId(string $value)
 * @method string getPrivileges()
 * @method Mage_Admin_Model_Rules setPrivileges(string $value)
 * @method int getAssertId()
 * @method Mage_Admin_Model_Rules setAssertId(int $value)
 * @method string getRoleType()
 * @method Mage_Admin_Model_Rules setRoleType(string $value)
 * @method string getPermission()
 * @method Mage_Admin_Model_Rules setPermission(string $value)
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Rules extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Admin_Model_Resource_Rules');
    }

    public function update() {
        $this->getResource()->update($this);
        return $this;
    }

    public function getCollection() {
        return Mage::getResourceModel('Mage_Admin_Model_Resource_Permissions_Collection');
    }

    public function saveRel() {
        $this->getResource()->saveRel($this);
        return $this;
    }
}
