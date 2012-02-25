<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Admin/_files/user.php
 * @group module:Mage_Widget
 */
class Mage_Widget_Adminhtml_Widget_Instance_ControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        $this->_session = new Mage_Admin_Model_Session();
        $this->_session->login('user', 'password');

        $this->getRequest()->setParam('type', 'Mage_Cms_Block_Widget_Page_Link');
        $this->getRequest()->setParam('package_theme', 'default-default');
    }

    public function testEditAction()
    {
        $this->dispatch('admin/widget_instance/edit');
        $this->assertContains('<option value="Mage_Cms_Block_Widget_Page_Link" selected="selected">',
            $this->getResponse()->getBody()
        );
    }

    public function testBlocksAction()
    {
        $this->dispatch('admin/widget_instance/blocks');
        $this->assertStringStartsWith('<select name="block" id=""', $this->getResponse()->getBody());
    }

    public function testTemplateAction()
    {
        $this->dispatch('admin/widget_instance/template');
        $this->assertStringStartsWith('<select name="template" id=""', $this->getResponse()->getBody());
    }
}
