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
class Mage_DesignEditor_Block_ToolbarTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_DesignEditor_Block_Toolbar(array('template' => 'toolbar.phtml'));
    }

    public function testToHtmlDesignEditorInactive()
    {
        $this->assertEmpty($this->_block->toHtml());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testToHtmlDesignEditorActive()
    {
        $this->assertNotEmpty($this->_block->toHtml());
    }
}
