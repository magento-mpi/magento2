<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_Core
     * @subpackage  integration_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * @group module:Mage_Core
     */
class Mage_Core_Block_Text_ListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;
    /**
     * @var Mage_Core_Block_Text_List
     */
    protected $_block;

    protected function setUp()
    {
        $this->_layout = new Mage_Core_Model_Layout;
        $this->_block = $this->_layout->createBlock('Mage_Core_Block_Text_List');
    }

    public function testToHtml()
    {
        $children = array(
            array('block1', 'Mage_Core_Block_Text', 'text1'),
            array('block2', 'Mage_Core_Block_Text', 'text2'),
            array('block3', 'Mage_Core_Block_Text', 'text3'),
        );
        foreach ($children as $child) {
            $this->_layout->addBlock($child[1], $child[0], $this->_block->getNameInLayout())->setText($child[2]);
        }
        $html = $this->_block->toHtml();
        $this->assertEquals('text1text2text3', $html);
    }

    public function testToHtmlWithContainer()
    {
        $listName = $this->_block->getNameInLayout();
        $block1 = $this->_layout->addBlock('Mage_Core_Block_Text', '', $listName);
        $this->_layout->insertContainer($listName, 'container');
        $block2 = $this->_layout->addBlock('Mage_Core_Block_Text', '', 'container');
        $block3 = $this->_layout->addBlock('Mage_Core_Block_Text', '', $listName);
        $block1->setText('text1');
        $block2->setText('text2');
        $block3->setText('text3');
        $html = $this->_block->toHtml();
        $this->assertEquals('text1text2text3', $html);
    }
}
