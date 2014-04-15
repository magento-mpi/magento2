<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Block;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRenderer()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $layout = $this->getMock('\Magento\Framework\View\Layout', array('getBlock'), array(), '', false);
        $template = $this->getMock('\Magento\Framework\View\Element\Template', array('getChildBlock'), array(), '', false);
        $layout->expects(
            $this->once()
        )->method(
            'getBlock'
        )->with(
            'customer_form_template'
        )->will(
            $this->returnValue($template)
        );
        $renderer = $this->getMock('\Magento\Framework\View\Element\Template', array(), array(), '', false);
        $template->expects($this->once())->method('getChildBlock')->with('text')->will($this->returnValue($renderer));

        $block = $objectHelper->getObject('Magento\CustomerCustomAttributes\Block\Form');
        $block->setLayout($layout);

        $this->assertEquals($renderer, $block->getRenderer('text'));
    }
}
