<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Options;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class AjaxTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Catalog\Block\Adminhtml\Product\Options\Ajax */
    protected $block;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Backend\Block\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var \Magento\Framework\Json\EncoderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $encoderInterfaceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $productFactoryMock;

    /** @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $coreHelperMock;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder('Magento\Backend\Block\Context')
            ->setMethods(['getEventManager', 'getScopeConfig', 'getLayout', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->encoderInterfaceMock = $this->getMock('Magento\Framework\Json\EncoderInterface');
        $this->productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', ['create']);
        $this->coreHelperMock = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);
        $this->registryMock = $this->getMock('Magento\Framework\Registry');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    /**
     *  Test protected `_toHtml` method via public `toHtml` method.
     */
    public function testToHtml()
    {
        $eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\Manager')
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();
        $eventManagerMock->expects($this->once())->method('dispatch')->will($this->returnValue(true));

        $scopeConfigMock = $this->getMockBuilder('\Magento\Framework\App\Config')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()->getMock();
        $scopeConfigMock->expects($this->once())->method('getValue')->withAnyParameters()
            ->will($this->returnValue(false));

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')->disableOriginalConstructor()
            ->setMethods(['setStoreId', 'load', 'getId', '__wakeup', '__sleep'])
            ->getMock();
        $productMock->expects($this->once())->method('setStoreId')->will($this->returnSelf());
        $productMock->expects($this->once())->method('load')->will($this->returnSelf());
        $productMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $optionsBlock = $this->getMockBuilder('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option')
            ->setMethods(['setIgnoreCaching', 'setProduct', 'getOptionValues'])
            ->disableOriginalConstructor()
            ->getMock();
        $optionsBlock->expects($this->once())->method('setIgnoreCaching')->with(true)->will($this->returnSelf());
        $optionsBlock->expects($this->once())->method('setProduct')->with($productMock)->will($this->returnSelf());
        $optionsBlock->expects($this->once())->method('getOptionValues')->will($this->returnValue([]));

        $layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout\Element\Layout')
            ->disableOriginalConstructor()
            ->setMethods(['createBlock'])
            ->getMock();
        $layoutMock->expects($this->once())->method('createBlock')
            ->with('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option')
            ->will($this->returnValue($optionsBlock));

        $requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->setMethods(['getParam'])
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())->method('getParam')->with('store')
            ->will($this->returnValue(0));

        $this->contextMock->expects($this->once())->method('getEventManager')
            ->will($this->returnValue($eventManagerMock));
        $this->contextMock->expects($this->once())->method('getScopeConfig')
            ->will($this->returnValue($scopeConfigMock));
        $this->contextMock->expects($this->once())->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $this->contextMock->expects($this->once())->method('getRequest')
            ->will($this->returnValue($requestMock));
        $this->registryMock->expects($this->once())->method('registry')
            ->with('import_option_products')
            ->will($this->returnValue([1]));
        $this->productFactoryMock->expects($this->once())->method('create')->will($this->returnValue($productMock));

        $this->block = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Block\Adminhtml\Product\Options\Ajax',
            [
                'context' => $this->contextMock,
                'jsonEncoder' => $this->encoderInterfaceMock,
                'productFactory' => $this->productFactoryMock,
                'coreData' => $this->coreHelperMock,
                'registry' => $this->registryMock
            ]
        );
        $this->block->toHtml();
    }
}
