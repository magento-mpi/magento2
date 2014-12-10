<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Block;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRenderer()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $layout = $this->getMock('\Magento\Framework\View\Layout', ['getBlock'], [], '', false);
        $template = $this->getMock(
            '\Magento\Framework\View\Element\Template',
            ['getChildBlock'],
            [],
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
        $renderer = $this->getMock('\Magento\Framework\View\Element\Template', [], [], '', false);
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
            ['getCustomerId'],
            [],
            '',
            false
        );
        $customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $entityMock = $this->getMock('stdClass', ['load']);
        $entityMock->expects($this->once())
            ->method('load')
            ->with(1);
        $modelFactoryMock = $this->getMock('Magento\Core\Model\Factory', ['create'], [], '', false);
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
