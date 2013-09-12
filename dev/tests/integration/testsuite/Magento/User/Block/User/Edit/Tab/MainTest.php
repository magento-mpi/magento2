<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_User_Block_User_Edit_Tab_MainTest extends Magento_Backend_Utility_Controller
{
    /**
     * @var Magento_User_Block_User_Edit_Tab_Main
     */
    protected $_block;

    /**
     * @var Magento_User_Model_User
     */
    protected $_user;

    public function setUp()
    {
        parent::setUp();
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        
        $this->_block = $objectManager->create('Magento_User_Block_User_Edit_Tab_Main');
        $this->_block->setArea('adminhtml');
        $this->_user = $objectManager->create('Magento_User_Model_User');

        $objectManager->get('Magento_Core_Model_Registry')->register('permissions_user', $this->_user);
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_user = null;
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->unregister('permissions_user');
        parent::tearDown();
    }

    public function testToHtmlPasswordFieldsExistingEntry()
    {
        $this->_user->loadByUsername(Magento_TestFramework_Bootstrap::ADMIN_NAME);
        $actualHtml = $this->_block->toHtml();
        $this->assertSelectCount(
            'input.required-entry[type="password"]', 0, $actualHtml,
            'All password fields have to be optional.'
        );
        $this->assertSelectCount(
            'input.validate-admin-password[type="password"][name="password"]', 1, $actualHtml
        );
        $this->assertSelectCount(
            'input.validate-cpassword[type="password"][name="password_confirmation"]', 1, $actualHtml
        );
    }

    public function testToHtmlPasswordFieldsNewEntry()
    {
        $actualHtml = $this->_block->toHtml();
        $this->assertSelectCount(
            'input.validate-admin-password.required-entry[type="password"][name="password"]', 1, $actualHtml
        );
        $this->assertSelectCount(
            'input.validate-cpassword.required-entry[type="password"][name="password_confirmation"]', 1, $actualHtml
        );
    }
}
