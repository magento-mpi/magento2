<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Block\Text;

class ListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Core\Block\Text\ListText
     */
    protected $_block;

    protected function setUp()
    {
        $this->_layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_block = $this->_layout->createBlock('Magento\Core\Block\Text\ListText');
    }

    public function testToHtml()
    {
        $children = array(
            array('block1', 'Magento\Core\Block\Text', 'text1'),
            array('block2', 'Magento\Core\Block\Text', 'text2'),
            array('block3', 'Magento\Core\Block\Text', 'text3'),
        );
        foreach ($children as $child) {
            $this->_layout->addBlock($child[1], $child[0], $this->_block->getNameInLayout())
                ->setText($child[2]);
        }
        $html = $this->_block->toHtml();
        $this->assertEquals('text1text2text3', $html);
    }

    public function testToHtmlWithContainer()
    {
        $listName = $this->_block->getNameInLayout();
        $block1 = $this->_layout->addBlock('Magento\Core\Block\Text', '', $listName);
        $this->_layout->addContainer('container', 'Container', array(), $listName);
        $block2 = $this->_layout->addBlock('Magento\Core\Block\Text', '', 'container');
        $block3 = $this->_layout->addBlock('Magento\Core\Block\Text', '', $listName);
        $block1->setText('text1');
        $block2->setText('text2');
        $block3->setText('text3');
        $html = $this->_block->toHtml();
        $this->assertEquals('text1text2text3', $html);
    }
}
