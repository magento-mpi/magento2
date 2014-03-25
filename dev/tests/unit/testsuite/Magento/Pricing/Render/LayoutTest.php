<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing\Render;

/**
 * Test class for \Magento\Pricing\Render\Layout
 */
class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Layout
     */
    protected $model;

    /**
     * @var  \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\View\LayoutFactory
     */
    protected $layoutFactory;

    public function setUp()
    {
        $this->layout = $this->getMock('Magento\View\LayoutInterface');

        $layoutFactory = $this->getMockBuilder('Magento\View\LayoutFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $layoutFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->layout));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Pricing\Render\Layout', array(
            'layoutFactory' => $layoutFactory
        ));
    }

    public function testAddHandle()
    {
        $handle = 'test_handle';

        $layoutProcessor = $this->getMock('Magento\View\Layout\ProcessorInterface');
        $layoutProcessor->expects($this->once())
            ->method('addHandle')
            ->with($handle);
        $this->layout->expects($this->once())
            ->method('getUpdate')
            ->will($this->returnValue($layoutProcessor));

        $this->model->addHandle($handle);
    }

    public function testLoadLayout()
    {
        $layoutProcessor = $this->getMock('Magento\View\Layout\ProcessorInterface');
        $layoutProcessor->expects($this->once())
            ->method('load');
        $this->layout->expects($this->once())
            ->method('getUpdate')
            ->will($this->returnValue($layoutProcessor));

        $this->layout->expects($this->once())
            ->method('generateXml');

        $this->layout->expects($this->once())
            ->method('generateElements');

        $this->model->loadLayout();
    }

    public function testGetBlock()
    {
        $blockName = 'block.name';

        $block = $this->getMock('Magento\View\Element\BlockInterface');

        $this->layout->expects($this->once())
            ->method('getBlock')
            ->with($blockName)
            ->will($this->returnValue($block));

        $this->assertEquals($block, $this->model->getBlock($blockName));
    }
}
