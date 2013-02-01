<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Block_MessagesTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Block_Messages */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Core_Block_Messages');
        $this->_block->getMessageCollection()->addMessage(new Mage_Core_Model_Message_Error('test'));
    }

    protected function tearDown()
    {
        unset($this->_block);
    }

    public function testGetHtml()
    {
        $this->assertContains('id="messages"', $this->_block->getHtml());
    }

    public function testGetGroupedHtml()
    {
        $this->assertContains('id="messages"', $this->_block->getGroupedHtml());
    }
}
