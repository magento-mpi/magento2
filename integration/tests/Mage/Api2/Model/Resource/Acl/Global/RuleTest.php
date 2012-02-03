<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API2 global ACL rule resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Global_RuleTest extends Magento_TestCase
{
    const ALLOWED_ATTRIBUTES = 'name,description,short_description,price';

    /**
     * Admin user data fixture
     *
     * @var Mage_Admin_Model_User
     */
    protected static $_admin;

    /**
     * API2 role data fixture
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected static $_role;

    /**
     * API2 rule data fixture
     *
     * @var Mage_Api2_Model_Acl_Global_Rule
     */
    protected static $_rule;

    /**
     * Set admin data fixture
     *
     * @static
     */
    public static function adminDataFixture()
    {
        $data = array(
            'firstname' => 'TestAdminUserFirstName' . mt_rand(),
            'lastname'  => 'TestAdminUserLastName' . mt_rand(),
            'email'     => 'testadminuseremail' . mt_rand() . '@example.com',
            'username'  => 'TestAdminUserName' . mt_rand(),
            'password'  => '123123q'
        );

        /** @var $user Mage_Admin_Model_User */
        $user = Mage::getModel('admin/user');
        $user->setData($data)->save();

        self::$_admin = $user;
    }

    /**
     * Set role data fixture
     *
     * @static
     */
    public static function roleDataFixture()
    {
        $data = array(
            'role_name' => 'TestRoleName' . mt_rand()
        );

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $role->setData($data)->save();

        self::$_role = $role;
    }

    /**
     * Set rule data fixture
     *
     * @static
     */
    public static function ruleDataFixture()
    {
        $data = array(
            'role_id'     => self::$_role->getId(),
            'resource_id' => 'test/resource',
            'privilege'   => 'create',
            'allowed_attributes' => self::ALLOWED_ATTRIBUTES
        );

        /** @var $role Mage_Api2_Model_Acl_Global_Rule */
        $rule = Mage::getModel('api2/acl_global_rule');
        $rule->setData($data)->save();

        self::$_rule = $rule;
    }

    /**
     * Test get allowed attributes
     *
     * @magentoDataFixture adminDataFixture
     * @magentoDataFixture roleDataFixture
     * @magentoDataFixture ruleDataFixture
     */
    public function testGetAllowedAttributes()
    {

    }
}
