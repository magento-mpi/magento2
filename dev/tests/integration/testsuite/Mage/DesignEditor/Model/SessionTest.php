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

class Mage_DesignEditor_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected static $_adminSession;

    /**
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_DesignEditor_Model_Session');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testIsDesignEditorActiveFalse()
    {
        $this->assertFalse($this->_model->isDesignEditorActive());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @magentoAppIsolation enabled
     */
    public function testIsDesignEditorActiveTrue()
    {
        $this->assertTrue($this->_model->isDesignEditorActive());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @magentoConfigFixture current_store admin/security/session_lifetime 100
     * @magentoAppIsolation enabled
     */
    public function testIsDesignEditorActiveAdminSessionExpired()
    {
        $this->assertTrue($this->_model->isDesignEditorActive());
        $this->_model->setUpdatedAt(time() - 101);
        $this->assertFalse($this->_model->isDesignEditorActive());
    }

    /**
     * @magentoDataFixture loginAdmin
     */
    public function testActivateDesignEditor()
    {
        $this->assertFalse($this->_model->isDesignEditorActive());
        $this->_model->activateDesignEditor();
        $this->assertTrue($this->_model->isDesignEditorActive());
    }

    public static function loginAdmin()
    {
        $auth = Mage::getModel('Mage_Backend_Model_Auth');
        self::$_adminSession = $auth->getAuthStorage();
        $auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    public static function loginAdminRollback()
    {
        $auth = Mage::getModel('Mage_Backend_Model_Auth');
        $auth->setAuthStorage(self::$_adminSession);
        $auth->logout();
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testDeactivateDesignEditor()
    {
        $this->assertTrue($this->_model->isDesignEditorActive());
        $this->_model->deactivateDesignEditor();
        $this->assertFalse($this->_model->isDesignEditorActive());
    }

    public function testIsHighlightingDisabled()
    {
        $this->assertFalse($this->_model->isHighlightingDisabled());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'off');
        $this->assertTrue($this->_model->isHighlightingDisabled());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'on');
        $this->assertFalse($this->_model->isHighlightingDisabled());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @depends testDeactivateDesignEditor
     * @depends testIsHighlightingDisabled
     */
    public function testIsHighlightingDisabledOnDeactivateDesignEditor()
    {
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'off');
        $this->assertTrue($this->_model->isHighlightingDisabled());
        $this->_model->deactivateDesignEditor();
        $this->assertFalse($this->_model->isHighlightingDisabled());
    }

    public function testSetThemeId()
    {
        $this->_model->setThemeId(0);
        $this->assertEquals(0, $this->_model->getThemeId());
    }
}
