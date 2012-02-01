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
class Mage_DesignEditor_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Do not backup/restore session data between tests
     *
     * @var bool
     */
    protected $backupGlobalsBlacklist = array('_SESSION');

    /**
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_model;

    /**
     * Cleanup session data
     */
    public static function tearDownAfterClass()
    {
        $_SESSION = array();
    }

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

    public function testActivateDesignEditor()
    {
        $this->assertFalse($this->_model->isDesignEditorActive());
        $this->_model->activateDesignEditor();
        $this->assertTrue($this->_model->isDesignEditorActive());
    }

    /**
     * @depends testActivateDesignEditor
     */
    public function testDeactivateDesignEditor()
    {
        $this->assertTrue($this->_model->isDesignEditorActive());
        $this->_model->deactivateDesignEditor();
        $this->assertFalse($this->_model->isDesignEditorActive());
    }
}
