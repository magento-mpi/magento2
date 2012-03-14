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
     * @var Mage_Admin_Model_Roles
     */
    protected $_role = null;
    /**
     * @var Mage_Admin_Model_User
     */
    protected $_user = null;

    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
    }
    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoConfigFixture admin_store catalog/enterprise_catalogpermissions/enabled 1
     */
    public function testValidateCatalogPermissionsWebsites()
    {
        $this->_initRole('websites');
        $this->_login();
        $this->dispatch('admin/catalog_category/edit/3');
        $result = $this->getResponse()->getBody();
        $this->assertContains('limited_website_ids', $result);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoConfigFixture admin_store catalog/enterprise_catalogpermissions/enabled 1
     */
    public function testValidateCatalogPermissionsStoreGroups()
    {
        $this->_initRole('stores');
        $this->_login();
        $this->dispatch('admin/catalog_category/edit/id/3');
        $result = $this->getResponse()->getBody();
        $this->assertContains('New Permission', $result);
        $this->assertContains('{{html_id}}_delete_button', $result);
    }

    protected function _initRole($scope)
    {
        $this->_role = new Mage_Admin_Model_Roles;
        $this->_role->setName($scope . 'Allowed')
            ->setGwsIsAll(0)
            ->setRoleType('G')
            ->setPid('1');
        if ('websites' == $scope) {
            $this->_role->setGwsWebsites(Mage::app()->getWebsite()->getId());
        } else {
            $this->_role->setGwsStoreGroups(Mage::app()->getWebsite()->getDefaultGroupId());
        }
        $this->_role->save();

        Mage::getModel('Mage_Admin_Model_Rules')
            ->setRoleId($this->_role->getId())
            ->setResources(array('all'))
            ->saveRel();
    }

    protected function _login()
    {
        $login = 'admingws_user';
        $password = '123123q';
        $this->_user = new Mage_Admin_Model_User;
        $this->_user->setFirstname('Name')
            ->setLastname('Lastname')
            ->setEmail('example@magento.com')
            ->setUsername($login)
            ->setPassword($password)
            ->setIsActive('1')
            ->save();
        $this->_user->setRoleIds(array($this->_role->getId()))
            ->setRoleUserId($this->_user->getUserId())
            ->saveRelations();
        $session = new Mage_Admin_Model_Session();
        $session->login($login, $password);
    }

    protected function tearDown()
    {
        $this->_user->delete();
        $this->_role->delete();
        $this->_user = null;
        $this->_role = null;
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }
}
