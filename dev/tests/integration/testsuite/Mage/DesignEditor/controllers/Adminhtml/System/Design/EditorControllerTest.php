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

class Mage_DesignEditor_Adminhtml_System_Design_EditorControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Assert that a page content contains the design editor form
     *
     * @param string $content
     */
    protected function _assertContainsDesignEditor($content)
    {
        $expectedFormAction = 'http://localhost/index.php/backend/admin/system_design_editor/launch/';
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

    public function testIndexActionSingleStore()
    {
        $this->dispatch('backend/admin/system_design_editor/index');
        $this->_assertContainsDesignEditor($this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     */
    public function testIndexActionMultipleStores()
    {
        $this->dispatch('backend/admin/system_design_editor/index');
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
        $this->dispatch('backend/admin/system_design_editor/launch');
        $this->assertTrue($session->isDesignEditorActive());

        $this->_requireSessionId();
        $this->assertRedirect($this->equalTo('http://localhost/index.php/?SID=' . $this->_session->getSessionId()));
    }

    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoConfigFixture fixturestore_store web/unsecure/base_link_url http://example.com/
     */
    public function testLaunchActionMultipleStores()
    {
        $this->getRequest()->setParam('store_id', Mage::app()->getStore('fixturestore')->getId());

        $session = new Mage_DesignEditor_Model_Session();
        $this->assertFalse($session->isDesignEditorActive());
        $this->dispatch('backend/admin/system_design_editor/launch');
        $this->assertTrue($session->isDesignEditorActive());

        $this->_requireSessionId();
        $expected = 'http://example.com/index.php/?SID=' . $this->_session->getSessionId() . '&___store=fixturestore';
        $this->assertRedirect($this->equalTo($expected));
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testExitAction()
    {
        $session = new Mage_DesignEditor_Model_Session();
        $this->assertTrue($session->isDesignEditorActive());
        $this->dispatch('backend/admin/system_design_editor/exit');

        $this->assertFalse($session->isDesignEditorActive());
        $this->assertContains(
            '<script type="text/javascript">window.close();</script>',
            $this->getResponse()->getBody()
        );
    }
}
