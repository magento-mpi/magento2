<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Customer\Edit;

class AbstractEditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit
     */
    protected $block;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->localeDateMock = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->layoutMock = $this->getMock('\Magento\Framework\View\LayoutInterface');
        $this->contextMock->expects($this->any())->method('getLayout')->will($this->returnValue($this->layoutMock));
        $this->contextMock
            ->expects($this->any())
            ->method('getLocaleDate')
            ->will($this->returnValue($this->localeDateMock));
        $methods =
            ['isSecure', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam', 'getCookie'];
        $requestMock = $this->getMock('Magento\Framework\App\RequestInterface', $methods);
        $this->contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($requestMock));
        $assertRepoMock = $this->getMock('\Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->contextMock
            ->expects($this->once())
            ->method('getAssetRepository')
            ->will($this->returnValue($assertRepoMock));

        $assertRepoMock->expects($this->once())->method('getUrlWithParams');
        $this->block = $this->getMockForAbstractClass('Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit',
            [
                $this->contextMock,
                $this->getMock('Magento\Core\Helper\Data', [], [], '', false),
                $this->getMock('Magento\Framework\Json\EncoderInterface'),
                $this->getMock('Magento\Framework\App\Cache\Type\Config', [], [], '', false),
                $this->getMock('Magento\Directory\Model\Resource\Region\CollectionFactory', [], [], '', false),
                $this->getMock('Magento\Directory\Model\Resource\Country\CollectionFactory', [], [], '', false),
                $this->getMock('Magento\Framework\Registry', [], [], '', false),
                $this->getMock('Magento\Customer\Model\Session', [], [], '', false),
                $this->getMock('Magento\GiftRegistry\Model\Attribute\Config', [], [], '', false),
                []
            ]
        );
    }

    public function testGetCalendarDateHtml()
    {
        $value = '07/24/14';
        $methods = ['setId', 'setName', 'setValue', 'setClass', 'setImage', 'setDateFormat', 'getHtml'];
        $block = $this->getMock('Magento\GiftRegistry\Block\Customer\Date', $methods, [], '', false);
        $this->localeDateMock
            ->expects($this->once())
            ->method('date')
            ->with(strtotime($value), null, null, false)
            ->will($this->returnValue($value));
        $this->localeDateMock
            ->expects($this->once())
            ->method('formatDate')
            ->with($value, \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM)
            ->will($this->returnValue($value));
        $this->localeDateMock
            ->expects($this->once())
            ->method('getDateFormat')
            ->with(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM)
            ->will($this->returnValue('format'));
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with('Magento\GiftRegistry\Block\Customer\Date')->will($this->returnValue($block));
        $block->expects($this->once())->method('setId')->with('id')->will($this->returnSelf());
        $block->expects($this->once())->method('setName')->with('name')->will($this->returnSelf());
        $block->expects($this->once())->method('setValue')->with($value)->will($this->returnSelf());
        $block->expects($this->once())
            ->method('setClass')
            ->with(' product-custom-option datetime-picker input-text validate-date')
            ->will($this->returnSelf());
        $block->expects($this->once())
            ->method('setImage')
            ->will($this->returnSelf());
        $block->expects($this->once())
            ->method('setDateFormat')
            ->with('format')
            ->will($this->returnSelf());
        $block->expects($this->once())->method('getHtml')->will($this->returnValue('expected_html'));
        $this->assertEquals('expected_html', $this->block->getCalendarDateHtml('name', 'id', $value));
    }
}
