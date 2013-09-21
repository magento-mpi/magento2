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

namespace Magento\User\Block\User\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
class MainTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @var \Magento\User\Block\User\Edit\Tab\Main
     */
    protected $_block;

    /**
     * @var \Magento\User\Model\User
     */
    protected $_user;

    public function setUp()
    {
        parent::setUp();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        
        $this->_block = $objectManager->create('Magento\User\Block\User\Edit\Tab\Main');
        $this->_block->setArea('adminhtml');
        $this->_user = $objectManager->create('Magento\User\Model\User');

        $objectManager->get('Magento\Core\Model\Registry')->register('permissions_user', $this->_user);
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_user = null;
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->unregister('permissions_user');
        parent::tearDown();
    }

    public function testToHtmlPasswordFieldsExistingEntry()
    {
        $this->_user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
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
