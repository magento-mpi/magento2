<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_AdminGws
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_AdminGws
 */
class Enterprise_AdminGws_Model_BlocksTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Possible scopes for admin role
     */
    const SCOPE_WEBSITES = 'websites';
    const SCOPE_STORES = 'stores';

    /**
     * Admin role
     *
     * @var Mage_Admin_Model_Roles
     */
    protected static $_role = null;

    /**
     * Admin user
     *
     * @var Mage_Admin_Model_User
     */
    protected static $_user = null;

    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
    }

    protected function tearDown()
    {
        self::$_role = null;
        self::$_user = null;
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }

    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoConfigFixture admin_store catalog/enterprise_catalogpermissions/enabled 1
     * @magentoDataFixture roleDataFixtureWebsites
     * @magentoDataFixture loginDataFixture
     */
    public function testValidateCatalogPermissionsWebsites()
    {
        $this->dispatch('admin/catalog_category/edit/3');
        $result = $this->getResponse()->getBody();
        $this->assertContains('limited_website_ids', $result);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoConfigFixture admin_store catalog/enterprise_catalogpermissions/enabled 1
     * @magentoDataFixture roleDataFixtureStores
     * @magentoDataFixture loginDataFixture
     */
    public function testValidateCatalogPermissionsStoreGroups()
    {
        $this->dispatch('admin/catalog_category/edit/id/3');
        $result = $this->getResponse()->getBody();
        $this->assertContains('New Permission', $result);
        $this->assertContains('{{html_id}}_delete_button', $result);
    }

    /**
     * Creates role with websites scope
     *
     * @static
     */
    public static function roleDataFixtureWebsites()
    {
        self::initRole(self::SCOPE_WEBSITES);
    }

    /**
     * Creates role with stores scope
     *
     * @static
     */
    public static function roleDataFixtureStores()
    {
        self::initRole(self::SCOPE_STORES);
    }

    /**
     * Creates role with specified scope
     *
     * @static
     * @param $scope string
     */
    protected static function initRole($scope)
    {
        self::$_role = new Mage_Admin_Model_Roles;
        self::$_role->setName($scope . 'Allowed')
            ->setGwsIsAll(0)
            ->setRoleType('G')
            ->setPid('1');
        if (self::SCOPE_WEBSITES == $scope) {
            self::$_role->setGwsWebsites(Mage::app()->getWebsite()->getId());
        } else {
            self::$_role->setGwsStoreGroups(Mage::app()->getWebsite()->getDefaultGroupId());
        }
        self::$_role->save();

        Mage::getModel('Mage_Admin_Model_Rules')
            ->setRoleId(self::$_role->getId())
            ->setResources(array('all'))
            ->saveRel();
    }

    /**
     * Creates admin user with current role and performs login for this user
     *
     * @static
     */
    public static function loginDataFixture()
    {
        if (is_null(self::$_role)) {
            throw new Magento_Exception('Can not create user: role does not exist');
        }
        $login = 'admingws_user';
        $password = '123123q';
        self::$_user = new Mage_Admin_Model_User;
        self::$_user->setFirstname('Name')
            ->setLastname('Lastname')
            ->setEmail('example@magento.com')
            ->setUsername($login)
            ->setPassword($password)
            ->setIsActive('1')
            ->save();
        self::$_user->setRoleIds(array(self::$_role->getId()))
            ->setRoleUserId(self::$_user->getUserId())
            ->saveRelations();
        $session = new Mage_Admin_Model_Session;
        $session->login($login, $password);
    }

    /**
     * Performs logout
     *
     * @static
     */
    public static function loginDataFixtureRollback()
    {
        $session = new Mage_Admin_Model_Session;
        $session->logout();
    }
}
