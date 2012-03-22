<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Admin
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Utility for managing admin user creation/destruction
 */
class Mage_Admin_Utility_User
{
    const CRED_USERNAME = 'user';
    const CRED_PASSWORD = 'password';

    /**
     * @var Mage_Admin_Utility_User
     */
    protected static  $_instance;

    /**
     * @var Mage_Admin_Model_User
     */
    protected $_user;

    /**
     * @var Mage_Admin_Model_Role
     */
    protected $_roleUser;

    /**
     * Protected constructor - just to prohibit manual creation of this class
     */
    protected function __construct()
    {
    }

    /**
     * Returns instance of the class
     *
     * @return Mage_Admin_Utility_User
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Creates admin user and other stuff, needed for him
     *
     * @return Mage_Admin_Utility_User
     */
    public function createAdmin()
    {
        if ($this->_user) {
            return $this;
        }

        $this->_user = new Mage_Admin_Model_User();
        $this->_user->setData(array(
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'email'     => 'admin@example.com',
            'username'  => self::CRED_USERNAME,
            'password'  => self::CRED_PASSWORD,
            'is_active' => 1
        ));
        $this->_user->save();

        $roleAdmin = new Mage_Admin_Model_Role();
        $roleAdmin->load('Administrators', 'role_name');

        $this->_roleUser = new Mage_Admin_Model_Role();
        $this->_roleUser->setData(array(
            'parent_id'  => $roleAdmin->getId(),
            'tree_level' => $roleAdmin->getTreeLevel() + 1,
            'role_type'  => Mage_Admin_Model_Acl::ROLE_TYPE_USER,
            'user_id'    => $this->_user->getId(),
            'role_name'  => $this->_user->getFirstname(),
        ));
        $this->_roleUser->save();

        return $this;
    }

    /**
     * Destroys created user and all his stuff
     * @return Mage_Admin_Utility_User
     */
    public function destroyAdmin()
    {
        if (!$this->_user) {
            return $this;
        }

        $this->_roleUser->delete();
        $this->_roleUser = null;

        $this->_user->delete();
        $this->_user = null;

        return $this;
    }
}
