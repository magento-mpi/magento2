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
 */
class Mage_DesignEditor_Adminhtml_System_Design_EditorControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Admin_Model_Session
     */
    protected  $_session;

    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        $this->_session = new Mage_Admin_Model_Session();
        $this->_session->login('user', 'password');
    }

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

    /**
     * Skip the current test, if session identifier is not defined in the environment
     */
    public function _requireSessionId()
    {
        if (!$this->_session->getSessionId()) {
            $this->markTestSkipped('Test requires environment with non-empty session identifier.');
        }
    }

    /**
     * Mark test skipped, if environment doesn't allow to send headers
     */
    protected function _requireSendingHeaders()
    {
        if (!Magento_Test_Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Test requires to send headers.');
        }
    }

    /**
     * @magentoDataFixture Mage/Admin/_files/user.php
     */
    public function testIndexActionSingleStore()
    {
        $this->dispatch('admin/system_design_editor/index');
        $this->_assertContainsDesignEditor($this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Admin/_files/user.php
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

    /**
     * @magentoDataFixture Mage/Admin/_files/user.php
     */
    public function testLaunchActionSingleStore()
    {
        $this->_requireSendingHeaders();

        $session = new Mage_DesignEditor_Model_Session();
        $this->assertFalse($session->isDesignEditorActive());
        $this->dispatch('admin/system_design_editor/launch');
        $this->assertTrue($session->isDesignEditorActive());

        $this->_requireSessionId();
        $this->assertRedirect('http://localhost/index.php/?SID=' . $this->_session->getSessionId());
    }

    /**
     * @magentoDataFixture Mage/Admin/_files/user.php
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoConfigFixture fixturestore_store web/unsecure/base_link_url http://example.com/
     */
    public function testLaunchActionMultipleStores()
    {
        $this->_requireSendingHeaders();

        $this->getRequest()->setParam('store_id', Mage::app()->getStore('fixturestore')->getId());

        $session = new Mage_DesignEditor_Model_Session();
        $this->assertFalse($session->isDesignEditorActive());
        $this->dispatch('admin/system_design_editor/launch');
        $this->assertTrue($session->isDesignEditorActive());

        $this->_requireSessionId();
        $this->assertRedirect(
            'http://example.com/index.php/?SID=' . $this->_session->getSessionId() . '&___store=fixturestore'
        );
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testExitAction()
    {
        $this->_requireSendingHeaders();

        $session = new Mage_DesignEditor_Model_Session();
        $this->assertTrue($session->isDesignEditorActive());
        $this->dispatch('admin/system_design_editor/exit');

        $this->assertFalse($session->isDesignEditorActive());
        $this->assertContains(
            '<script type="text/javascript">window.close();</script>',
            $this->getResponse()->getBody()
        );
    }
}
