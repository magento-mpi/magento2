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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translateInline;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfig;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()->getMock();

        $layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()->getMock();

        $processor = $this->getMockBuilder('Magento\Core\Model\Layout\Merge')
            ->disableOriginalConstructor()->getMock();

        $processor->expects($this->any())
            ->method('addPageHandles')
            ->will($this->returnArgument(0));

        $layoutMock->expects($this->any())
            ->method('getUpdate')
            ->will($this->returnValue($processor));

        $this->context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $request = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()->getMock();

        $request->expects($this->any())
            ->method('getFullActionName')
            ->will($this->returnValue('Full_Action_Name'));

        $this->context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->layoutFactory = $this->getMockBuilder('Magento\Framework\View\LayoutFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()->getMock();

        $this->translateInline = $this->getMockBuilder('Magento\Framework\Translate\InlineInterface')
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $this->pageConfig = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()->getMockForAbstractClass();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->page = $objectManagerHelper->getObject(
            'Magento\Framework\View\Result\Page',
            [
                'context' => $this->context,
                'layoutFactory' => $this->layoutFactory,
                'translateInline' => $this->translateInline,
                'pageConfig' => $this->pageConfig,
            ]
        );
    }

    public function testGetDefaultLayoutHandle()
    {
        $this->assertEquals('full_action_name', $this->page->getDefaultLayoutHandle());
    }

    public function testAddPageLayoutHandles()
    {
        $parameters = ['key_one' => 'val_one', 'key_two' => 'val_two'];
        $expected = ['full_action_name', 'full_action_name_key_one_val_one', 'full_action_name_key_two_val_two'];
        $this->assertEquals($expected, $this->page->addPageLayoutHandles($parameters));
    }
}
