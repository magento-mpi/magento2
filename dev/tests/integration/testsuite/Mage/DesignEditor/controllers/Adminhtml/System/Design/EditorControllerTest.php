<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_DesignEditor
 * @magentoDataFixture Mage/Admin/_files/admin_user_logged_in.php
 * @magentoDataFixture Mage/Adminhtml/_files/form_key_disabled.php
 */
class Mage_DesignEditor_Adminhtml_System_Design_EditorControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Assert that a page content contains the design editor form
     *
     * @param string $content
     */
    protected function _assertContainsDesignEditor($content)
    {
        $expectedFormAction = 'http://localhost/index.php/admin/system_design_editor/launch/';
        $this->assertContains('Visual Design Editor', $content);
        $this->assertContains('<form id="edit_form" action="' . $expectedFormAction, $content);
        $this->assertContains("editForm = new varienForm('edit_form'", $content);
        $this->assertContains('onclick="editForm.submit();"', $content);
    }

    public function _requireSessionId()
    {
        if (!session_id()) {
            $this->markTestSkipped('Test requires environment with non-empty session identifier.');
        }
    }

    public function testIndexActionSingleStore()
    {
        $this->dispatch('admin/system_design_editor/index');
        $this->_assertContainsDesignEditor($this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Admin/_files/admin_user_logged_in.php
     * @magentoDataFixture Mage/Adminhtml/_files/form_key_disabled.php
     * @magentoDataFixture Mage/Core/_files/store.php
     */
    public function testIndexActionMultipleStores()
    {
        $this->dispatch('admin/system_design_editor/index');
        $responseBody = $this->getResponse()->getBody();
        $this->_assertContainsDesignEditor($responseBody);
        $this->assertContains('<select id="store_id" name="store_id"', $responseBody);
        $this->assertContains('<label for="store_id">Store View', $responseBody);
        $this->assertContains('Fixture Store</option>', $responseBody);
    }

    public function testLaunchActionSingleStore()
    {
        $session = new Mage_DesignEditor_Model_Session();
        $this->assertFalse($session->isDesignEditorActive());
        $this->dispatch('admin/system_design_editor/launch');
        $this->assertTrue($session->isDesignEditorActive());

        $this->_requireSessionId();
        $this->assertRedirect('http://localhost/index.php/?SID=' . session_id());
    }

    /**
     * @magentoDataFixture Mage/Admin/_files/admin_user_logged_in.php
     * @magentoDataFixture Mage/Adminhtml/_files/form_key_disabled.php
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoConfigFixture fixturestore_store web/unsecure/base_link_url http://example.com/
     */
    public function testLaunchActionMultipleStores()
    {
        $this->getRequest()->setParam('store_id', Mage::app()->getStore('fixturestore')->getId());

        $session = new Mage_DesignEditor_Model_Session();
        $this->assertFalse($session->isDesignEditorActive());
        $this->dispatch('admin/system_design_editor/launch');
        $this->assertTrue($session->isDesignEditorActive());

        $this->_requireSessionId();
        $this->assertRedirect('http://example.com/index.php/?SID=' . session_id() . '&___store=fixturestore');
    }
}
