<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAddButtonId()
    {
        $button = new \Magento\Framework\Object();

        $itemsBlock = $this->getMock('Magento\Framework\Object', array('getChildBlock'));
        $itemsBlock->expects(
            $this->atLeastOnce()
        )->method(
            'getChildBlock'
        )->with(
            'add_button'
        )->will(
            $this->returnValue($button)
        );

        $layout = $this->getMock('Magento\Framework\Object', array('getBlock'));
        $layout->expects(
            $this->atLeastOnce()
        )->method(
            'getBlock'
        )->with(
            'admin.product.bundle.items'
        )->will(
            $this->returnValue($itemsBlock)
        );

        $block = $this->getMock(
            'Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option',
            array('getLayout'),
            array(),
            '',
            false
        );
        $block->expects($this->atLeastOnce())->method('getLayout')->will($this->returnValue($layout));

        $this->assertNotEquals(42, $block->getAddButtonId());
        $button->setId(42);
        $this->assertEquals(42, $block->getAddButtonId());
    }
}
