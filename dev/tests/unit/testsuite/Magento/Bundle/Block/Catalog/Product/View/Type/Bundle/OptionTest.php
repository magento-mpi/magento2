<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Block\Catalog\Product\View\Type\Bundle;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option
     */
    protected $_block;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_block =
            $objectManagerHelper->getObject('\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option');

        $product = $this->getMock(
            '\Magento\Catalog\Model\Product', array('hasPreconfiguredValues', 'getPreconfiguredValues', '__wakeup'),
            array(), '', false
        );
        $product->expects($this->atLeastOnce())->method('hasPreconfiguredValues')->will($this->returnValue(true));
        $product->expects($this->atLeastOnce())
            ->method('getPreconfiguredValues')
            ->will($this->returnValue(new \Magento\Object(array('bundle_option' => array(15 => 315, 16 => 316)))));

        $this->_block->setData('product', $product);
    }

    public function testSetOption()
    {
        $option = $this->getMock('\Magento\Bundle\Model\Option', array(), array(), '', false);
        $option->expects($this->any())->method('getId')->will($this->returnValue(15));

        $otherOption = $this->getMock('\Magento\Bundle\Model\Option', array(), array(), '', false);
        $otherOption->expects($this->any())->method('getId')->will($this->returnValue(16));

        $selection = $this->getMock(
            '\Magento\Catalog\Model\Product', array('getSelectionId', '__wakeup'), array(), '', false
        );
        $selection->expects($this->atLeastOnce())->method('getSelectionId')->will($this->returnValue(315));

        $this->assertSame($this->_block, $this->_block->setOption($option));
        $this->assertTrue($this->_block->isSelected($selection));

        $this->_block->setOption($otherOption);
        $this->assertFalse(
            $this->_block->isSelected($selection),
            'Selected value should change after new option is set'
        );
    }
}
