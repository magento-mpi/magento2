<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\View\Result;

/**
 * Result Page Test
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Result\Page
     */
    protected $page;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\Core\Model\Layout\Merge|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMerge;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfig;

    protected function setUp()
    {
        $layout = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutMerge = $this->getMockBuilder('Magento\Core\Model\Layout\Merge')
            ->disableOriginalConstructor()
            ->getMock();

        $layout->expects($this->any())
            ->method('getUpdate')
            ->will($this->returnValue($this->layoutMerge));

        $this->request = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageConfig = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->context = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $this->context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));

        $this->context->expects($this->any())
            ->method('getPageConfig')
            ->will($this->returnValue($this->pageConfig));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->page = $objectManagerHelper->getObject(
            'Magento\Framework\View\Result\Page',
            [
                'context' => $this->context,
            ]
        );
    }

    public function testInitLayout()
    {
        $handleDefault = 'default';
        $fullActionName = 'full_action_name';
        $this->request->expects($this->any())
            ->method('getFullActionName')
            ->will($this->returnValue($fullActionName));

        $this->layoutMerge->expects($this->at(0))
            ->method('addHandle')
            ->with($handleDefault)
            ->willReturnSelf();
        $this->layoutMerge->expects($this->at(1))
            ->method('addHandle')
            ->with($fullActionName)
            ->willReturnSelf();
        $this->layoutMerge->expects($this->at(2))
            ->method('isLayoutDefined')
            ->willReturn(false);

        $this->assertEquals($this->page, $this->page->initLayout());
    }

    public function testInitLayoutLayoutDefined()
    {
        $handleDefault = 'default';
        $fullActionName = 'full_action_name';
        $this->request->expects($this->any())
            ->method('getFullActionName')
            ->will($this->returnValue($fullActionName));

        $this->layoutMerge->expects($this->at(0))
            ->method('addHandle')
            ->with($handleDefault)
            ->willReturnSelf();
        $this->layoutMerge->expects($this->at(1))
            ->method('addHandle')
            ->with($fullActionName)
            ->willReturnSelf();
        $this->layoutMerge->expects($this->at(2))
            ->method('isLayoutDefined')
            ->willReturn(true);
        $this->layoutMerge->expects($this->at(3))
            ->method('removeHandle')
            ->with($handleDefault)
            ->willReturnSelf();

        $this->assertEquals($this->page, $this->page->initLayout());
    }

    public function testGetConfig()
    {
        $this->assertEquals($this->pageConfig, $this->page->getConfig());
    }

    public function testGetDefaultLayoutHandle()
    {
        $fullActionName = 'Full_Action_Name';
        $expectedFullActionName = 'full_action_name';

        $this->request->expects($this->any())
            ->method('getFullActionName')
            ->will($this->returnValue($fullActionName));

        $this->assertEquals($expectedFullActionName, $this->page->getDefaultLayoutHandle());
    }

    public function testAddPageLayoutHandles()
    {
        $fullActionName = 'Full_Action_Name';
        $defaultHandle = null;
        $parameters = [
            'key_one' => 'val_one',
            'key_two' => 'val_two'
        ];
        $expected = [
            'full_action_name',
            'full_action_name_key_one_val_one',
            'full_action_name_key_two_val_two'
        ];
        $this->request->expects($this->any())
            ->method('getFullActionName')
            ->will($this->returnValue($fullActionName));

        $this->layoutMerge->expects($this->any())
            ->method('addPageHandles')
            ->with($expected)
            ->willReturn(true);

        $this->assertTrue($this->page->addPageLayoutHandles($parameters, $defaultHandle));
    }

    public function testAddPageLayoutHandlesWithDefaultHandle()
    {
        $defaultHandle = 'default_handle';
        $parameters = [
            'key_one' => 'val_one',
            'key_two' => 'val_two'
        ];
        $expected = [
            'default_handle',
            'default_handle_key_one_val_one',
            'default_handle_key_two_val_two'
        ];
        $this->request->expects($this->never())
            ->method('getFullActionName');

        $this->layoutMerge->expects($this->any())
            ->method('addPageHandles')
            ->with($expected)
            ->willReturn(true);

        $this->assertTrue($this->page->addPageLayoutHandles($parameters, $defaultHandle));
    }
}
