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
        $template = $this->getMock(
            '\Magento\Framework\View\Element\Template',
            array('getChildBlock'),
            array(),
            '',
            false
        );
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

    public function testGetEntity()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $customerSessionMock = $this->getMock(
            'Magento\Customer\Model\Session',
            array('getCustomerId'),
            array(),
            '',
            false
        );
        $customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $entityMock = $this->getMock('stdClass', array('load'));
        $entityMock->expects($this->once())
            ->method('load')
            ->with(1);
        $modelFactoryMock = $this->getMock('Magento\Core\Model\Factory', array('create'), array(), '', false);
        $modelFactoryMock->expects($this->once())
            ->method('create')
            ->with('stdClass')
            ->will($this->returnValue($entityMock));

        /** @var \Magento\CustomerCustomAttributes\Block\Form $block */
        $block = $objectHelper->getObject(
            'Magento\CustomerCustomAttributes\Block\Form',
            [
                'modelFactory' => $modelFactoryMock,
                'customerSession' => $customerSessionMock,
            ]
        );
        $block->setEntityModelClass('stdClass');
        $block->getEntity();
    }
}
