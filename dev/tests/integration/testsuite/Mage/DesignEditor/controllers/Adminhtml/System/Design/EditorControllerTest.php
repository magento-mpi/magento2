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
     * Identifier theme
     *
     * @var int
     */
    protected static $_themeId;

    /**
     * Create theme is db
     */
    public static function prepareTheme()
    {
        $theme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme->setData(array(
            'theme_code'           => 'default',
            'package_code'         => 'default',
            'area'                 => 'frontend',
            'parent_id'            => null,
            'theme_path'           => 'default/demo',
            'theme_version'        => '2.0.0.0',
            'theme_title'          => 'Default',
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*',
            'is_featured'          => '0'
        ));
        $theme->save();
        self::$_themeId = $theme->getId();
    }

    /**
     * Delete theme from db
     */
    public static function prepareThemeRollback()
    {
        $theme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme->load(self::$_themeId)->delete();
    }

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
        $this->assertContains("jQuery('#edit_form').form()", $content);
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
        $this->markTestIncomplete('Will be fixed in MAGETWO-5635');

        $this->dispatch('backend/admin/system_design_editor/index');
        $this->_assertContainsDesignEditor($this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoConfigFixture fixturestore_store web/unsecure/base_link_url http://example.com/
     */
    public function testIndexActionMultipleStores()
    {
        $this->markTestIncomplete('Will be fixed in MAGETWO-5635');

        $this->dispatch('backend/admin/system_design_editor/index');
        $responseBody = $this->getResponse()->getBody();
        $this->_assertContainsDesignEditor($responseBody);
        $this->assertContains('id="store_id" name="store_id"', $responseBody);
        $this->assertContains('for="store_id"', $responseBody);
        $this->assertContains('Store View', $responseBody);
        $this->assertContains('Fixture Store</option>', $responseBody);
    }

    /**
     * @magentoDataFixture prepareTheme
     */
    public function testLaunchActionSingleStore()
    {
        $session = Mage::getModel('Mage_DesignEditor_Model_Session');
        $this->assertFalse($session->isDesignEditorActive());
        $this->getRequest()->setParam('theme_id', self::$_themeId);
        $this->dispatch('backend/admin/system_design_editor/launch');
        $this->assertTrue($session->isDesignEditorActive());

        $this->_requireSessionId();
        $this->assertRedirect($this->equalTo('http://localhost/index.php/?SID=' . $this->_session->getSessionId()));
    }

    public function testLaunchActionSingleStoreWrongThemeId()
    {
        $session = Mage::getObjectManager()->create('Mage_DesignEditor_Model_Session');
        $this->assertFalse($session->isDesignEditorActive());
        $this->getRequest()->setParam('theme_id', 999);
        $this->dispatch('backend/admin/system_design_editor/launch');
        $this->assertFalse($session->isDesignEditorActive());

        $this->_requireSessionId();
        $expected = 'http://localhost/index.php/backend/admin/system_design_editor/index/';
        $this->assertRedirect($this->stringStartsWith($expected));
    }

    /**
     * @magentoDataFixture prepareTheme
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoConfigFixture fixturestore_store web/unsecure/base_link_url http://example.com/
     */
    public function testLaunchActionMultipleStores()
    {
        $this->getRequest()->setParam('store_id', Mage::app()->getStore('fixturestore')->getId());

        $session = Mage::getModel('Mage_DesignEditor_Model_Session');
        $this->assertFalse($session->isDesignEditorActive());
        $this->getRequest()->setParam('theme_id', self::$_themeId);
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
        $session = Mage::getModel('Mage_DesignEditor_Model_Session');
        $this->assertTrue($session->isDesignEditorActive());
        $this->dispatch('backend/admin/system_design_editor/exit');

        $this->assertFalse($session->isDesignEditorActive());
        $this->assertContains(
            '<script type="text/javascript">window.close();</script>',
            $this->getResponse()->getBody()
        );
    }
}
