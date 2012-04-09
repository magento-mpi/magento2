<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 User Admin Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_User_Admin extends Mage_Api2_Model_Auth_User_Abstract
{
    /**
     * User type
     */
    const USER_TYPE = 'admin';

    /**
     * Retrieve user human-readable label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('Mage_Api2_Helper_Data')->__('Admin');
    }

    /**
     * Retrieve user role
     *
     * @return int
     * @throws Exception
     */
    public function getRole()
    {
        if (!$this->_role) {
            if (!$this->getUserId()) {
                throw new Exception('Admin identifier is not set');
            }

            /** @var $collection Mage_Api2_Model_Resource_Acl_Global_Role_Collection */
            $collection = Mage::getModel('Mage_Api2_Model_Acl_Global_Role')->getCollection();
            $collection->addFilterByAdminId($this->getUserId());

            /** @var $role Mage_Api2_Model_Acl_Global_Role */
            $role = $collection->getFirstItem();
            if (!$role->getId()) {
                throw new Exception('Admin role not found');
            }

            $this->setRole($role->getId());
        }

        return $this->_role;
    }

    /**
     * Retrieve user type
     *
     * @return string
     */
    public function getType()
    {
        return self::USER_TYPE;
    }

    /**
     * Set user role
     *
     * @param int $role
     * @return Mage_Api2_Model_Auth_User_Admin
     * @throws Exception
     */
    public function setRole($role)
    {
        if ($this->_role) {
            throw new Exception('Admin role has been already set');
        }
        $this->_role = $role;

        return $this;
    }
}
