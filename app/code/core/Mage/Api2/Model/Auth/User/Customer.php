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
 * API2 User Customer Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_User_Customer extends Mage_Api2_Model_Auth_User_Abstract
{
    /**
     * User type
     */
    const USER_TYPE = 'customer';

    /**
     * Retrieve user human-readable label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('Mage_Api2_Helper_Data')->__('Customer');
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
     * Retrieve user role
     *
     * @return int
     */
    public function getRole()
    {
        if (!$this->_role) {
            /** @var $role Mage_Api2_Model_Acl_Global_Role */
            $role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role')->load(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID);
            if (!$role->getId()) {
                throw new Exception('Customer role not found');
            }

            $this->_role = Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID;
        }

        return $this->_role;
    }
}
