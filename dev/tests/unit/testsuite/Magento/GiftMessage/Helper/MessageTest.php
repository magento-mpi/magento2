<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Helper;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutFactoryMock;

    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $helper;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->layoutFactoryMock = $this->getMock('\Magento\Framework\View\LayoutFactory', array(), array(), '', false);

        $this->helper = $objectManager->getObject('\Magento\GiftMessage\Helper\Message', array(
            'layoutFactory' => $this->layoutFactoryMock,
            'skipMessageCheck' => array('onepage_checkout'),
        ));
    }

    /**
     * Make sure that isMessagesAvailable is not called
     */
    public function testGetInlineForCheckout()
    {
        $expectedHtml = '<a href="here">here</a>';
        $layoutMock = $this->getMock('\Magento\Framework\View\Layout', array(), array(), '', false);
        $entityMock = $this->getMock('\Magento\Object', array(), array(), '', false);
        $inlineMock = $this->getMock(
            'Magento\GiftMessage\Block\Message\Inline',
            array('setId', 'setDontDisplayContainer', 'setEntity', 'setType', 'toHtml'),
            array(),
            '',
            false
        );

        $this->layoutFactoryMock->expects($this->once())->method('create')->will($this->returnValue($layoutMock));
        $layoutMock->expects($this->once())->method('createBlock')->will($this->returnValue($inlineMock));

        $inlineMock->expects($this->once())->method('setId')->will($this->returnSelf());
        $inlineMock->expects($this->once())->method('setDontDisplayContainer')->will($this->returnSelf());
        $inlineMock->expects($this->once())->method('setEntity')->with($entityMock)->will($this->returnSelf());
        $inlineMock->expects($this->once())->method('setType')->will($this->returnSelf());
        $inlineMock->expects($this->once())->method('toHtml')->will($this->returnValue($expectedHtml));

        $this->assertEquals($expectedHtml, $this->helper->getInline('onepage_checkout', $entityMock));
    }
}
