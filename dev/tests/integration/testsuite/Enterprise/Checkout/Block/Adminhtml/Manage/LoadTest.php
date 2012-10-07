<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Block_Adminhtml_Manage_LoadTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    /** @var Enterprise_Checkout_Block_Adminhtml_Manage_Load */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = new Mage_Core_Model_Layout;
        $this->_block = $this->_layout->createBlock('Enterprise_Checkout_Block_Adminhtml_Manage_Load');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_layout = null;
    }

    public function testToHtml()
    {
        $blockName1 = 'block1';
        $blockName2 = 'block2';
        $containerName = 'container';
        $content1 = 'Content 1';
        $content2 = 'Content 2';
        $containerContent = 'Content in container';

        $parent = $this->_block->getNameInLayout();
        $this->_layout->addBlock('Mage_Core_Block_Text', $blockName1, $parent)->setText($content1);
        $this->_layout->addContainer($containerName, 'Container', array(), $parent);
        $this->_layout->addBlock('Mage_Core_Block_Text', '', $containerName)->setText($containerContent);
        $this->_layout->addBlock('Mage_Core_Block_Text', $blockName2, $parent)->setText($content2);

        $result = $this->_block->toHtml();
        $expectedDecoded = array(
            $blockName1    => $content1,
            $containerName => $containerContent,
            $blockName2    => $content2
        );
        $this->assertEquals($expectedDecoded, Mage::helper('Mage_Core_Helper_Data')->jsonDecode($result));
    }
}
