<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Catalog\Product\View\Type;

class BundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    /**
     * @var \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle
     */
    protected $_bundleBlock;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_bundleBlock = $objectHelper->getObject('Magento\Bundle\Block\Catalog\Product\View\Type\Bundle');
    }

    public function testGetOptionHtmlNoRenderer()
    {
        $option = $this->getMock('\Magento\Bundle\Model\Option', array('getType', '__wakeup'), array(), '', false);
        $option->expects($this->exactly(2))->method('getType')->will($this->returnValue('checkbox'));

        $this->assertEquals(
            'There is no defined renderer for "checkbox" option type.',
            $this->_bundleBlock->getOptionHtml($option)
        );
    }

    public function testGetOptionHtml()
    {
        $option = $this->getMock('\Magento\Bundle\Model\Option', array('getType', '__wakeup'), array(), '', false);
        $option->expects($this->exactly(1))->method('getType')->will($this->returnValue('checkbox'));

        $optionBlock = $this->getMock(
            '\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Checkbox',
            array('setOption', 'toHtml', 'getPriceBlockTypes'),
            array(),
            '',
            false
        );
        $optionBlock->expects($this->any())->method('setOption')->will($this->returnValue($optionBlock));
        $optionBlock->expects($this->any())->method('getPriceBlockTypes')->will($this->returnValue(array()));
        $optionBlock->expects($this->any())->method('toHtml')->will($this->returnValue('option html'));
        $layout = $this->getMock('\Magento\Core\Model\Layout', array('getChildName', 'getBlock'), array(), '', false);
        $layout->expects($this->any())->method('getChildName')->will($this->returnValue('name'));
        $layout->expects($this->any())->method('getBlock')->will($this->returnValue($optionBlock));
        $this->_bundleBlock->setLayout($layout);

        $this->assertEquals('option html', $this->_bundleBlock->getOptionHtml($option));
    }
}
