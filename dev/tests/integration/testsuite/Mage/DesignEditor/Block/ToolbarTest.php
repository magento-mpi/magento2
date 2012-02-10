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

    /**
     * Isolation has been raised because block pollutes the registry
     *
     * @magentoAppIsolation enabled
     */
    public function testToHtmlDesignEditorInactive()
    {
        $this->assertEmpty($this->_block->toHtml());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testToHtmlDesignEditorActive()
    {
        $this->assertNotEmpty($this->_block->toHtml());
        $this->assertContains('title="Exit" class="vde_button">', $this->_block->toHtml());
    }

    public function testGetExitUrl()
    {
        $expected = 'http://localhost/index.php/admin/system_design_editor/exit/';
        $this->assertContains($expected, $this->_block->getExitUrl());
    }

    public function testGetMessages()
    {
        $messageError = new Mage_Core_Model_Message_Error('test error');
        $messageSuccess = new Mage_Core_Model_Message_Error('test success');
        $messages = new Mage_Core_Model_Message_Collection();
        $messages->addMessage($messageError);
        $messages->addMessage($messageSuccess);

        $session = $this->getMock('Mage_DesignEditor_Model_Session');
        $session->expects($this->atLeastOnce())
            ->method('getMessages')
            ->with(true)
            ->will($this->returnValue($messages));

        $block = $this->getMock('Mage_DesignEditor_Block_Toolbar', array('_getSession'));
        $block->expects($this->atLeastOnce())
            ->method('_getSession')
            ->will($this->returnValue($session));

        $blockMessages = $block->getMessages();
        $this->assertInternalType('array', $blockMessages);
        $this->assertCount(2, $blockMessages);
        $this->assertContains($messageError, $blockMessages);
        $this->assertContains($messageSuccess, $blockMessages);
    }
}
