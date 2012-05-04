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
     * @var Mage_Admin_Model_Session
     */
    protected static $_adminSession;

    /**
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_DesignEditor_Model_Session();
    }

    public function testIsDesignEditorActiveFalse()
    {
        $this->assertFalse($this->_model->isDesignEditorActive());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testIsDesignEditorActiveTrue()
    {
        $this->assertTrue($this->_model->isDesignEditorActive());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @magentoConfigFixture current_store admin/security/session_lifetime 100
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
        self::$_adminSession = new Mage_Admin_Model_Session();
        self::$_adminSession->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    public static function loginAdminRollback()
    {
        self::$_adminSession->logout();
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testDeactivateDesignEditor()
    {
        $this->assertTrue($this->_model->isDesignEditorActive());
        $this->_model->deactivateDesignEditor();
        $this->assertFalse($this->_model->isDesignEditorActive());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'off');
        $this->assertTrue($this->_model->isHighlightingDisabled());
    }

    public function testIsHighlightingDisabled()
    {
        $this->assertFalse($this->_model->isHighlightingDisabled());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'off');
        $this->assertTrue($this->_model->isHighlightingDisabled());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'on');
        $this->assertFalse($this->_model->isHighlightingDisabled());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'any');
        $this->assertFalse($this->_model->isHighlightingDisabled());
    }

    public function testSetSkin()
    {
        $this->_model->setSkin('default/default/blank');
        $this->assertEquals('default/default/blank', $this->_model->getSkin());
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testSetSkinWrongValue()
    {
        $this->_model->setSkin('wrong/skin/applied');
    }
}
