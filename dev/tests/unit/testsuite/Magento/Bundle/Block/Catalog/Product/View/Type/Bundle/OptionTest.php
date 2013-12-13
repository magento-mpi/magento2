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
        $this->_block = new \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option(
            $this->getMock('\Magento\View\Element\Template\Context', array(), array(), '', false),
            $this->getMock('\Magento\Json\EncoderInterface', array(), array(), '', false),
            $this->getMock('\Magento\Catalog\Helper\Data', array(), array(), '', false),
            $this->getMock('\Magento\Tax\Helper\Data', array(), array(), '', false),
            $this->getMock('\Magento\Core\Model\Registry', array(), array(), '', false),
            $this->getMock('\Magento\Stdlib\String', array(), array(), '', false),
            $this->getMock('\Magento\Math\Random', array(), array(), '', false),
            $this->getMock('\Magento\Tax\Model\Calculation', array(), array(), '', false),
            array()
        );
    }

    public function testSetOption()
    {
        $selection = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);

        $product = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('hasPreconfiguredValues', '__wakeup'), array(), '', false);
        $product->expects($this->exactly(2))
            ->method('hasPreconfiguredValues')
            ->will($this->returnValue(false));

        $this->_block->setData('product', $product);

        $option = $this->getMock('\Magento\Bundle\Model\Option', array(), array(), '', false);

        $result = $this->_block->setOption($option);
        $this->assertInstanceOf('\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option', $result);
        $this->assertSame($option, $this->_block->getOption());

        /**
         * here system should go into
         * \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option::_getSelectedOptions() and run
         * $product->hasPreconfiguredValues() first time
         */
        $this->_block->isSelected($selection);

        /**
         * setOption() sets _selectedOptions to null
         */
        $this->_block->setOption($option);

        /**
         * here system should go into
         * \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option::_getSelectedOptions() and run
         * $product->hasPreconfiguredValues() second time
         */
        $this->_block->isSelected($selection);
    }
}
