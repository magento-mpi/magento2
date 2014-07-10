<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

/**
 * Class LayoutTest
  */
class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockFactoryMock;

    /**
     * @var \Magento\Framework\View\Layout\ProcessorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processorFactoryMock;

    /**
     * @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appStateMock;

    /**
     * @var \Magento\Framework\View\Design\Theme\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeResolverMock;

    /**
     * @var \Magento\Core\Model\Layout\Merge|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processorMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $schStructureMock;

    protected function setUp()
    {
        $this->structureMock = $this->getMockBuilder('Magento\Framework\Data\Structure')
            ->disableOriginalConstructor()
            ->getMock();
        $this->blockFactoryMock = $this->getMockBuilder('Magento\Framework\View\Element\BlockFactory')
            ->setMethods(['createBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->processorFactoryMock = $this->getMock(
            'Magento\Framework\View\Layout\ProcessorFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->appStateMock = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $this->themeResolverMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Design\Theme\ResolverInterface'
        );
        $this->processorMock = $this->getMock('Magento\Core\Model\Layout\Merge', [], [], '', false);
        $this->schStructureMock = $this->getMock('Magento\Framework\View\Layout\ScheduledStructure', [], [], '', false);
        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Framework\View\Layout',
            array(
                'structure' => $this->structureMock,
                'blockFactory' => $this->blockFactoryMock,
                'themeResolver' => $this->themeResolverMock,
                'processorFactory' => $this->processorFactoryMock,
                'appState' => $this->appStateMock,
                'eventManager' => $this->eventManagerMock,
                'scheduledStructure' => $this->schStructureMock
            )
        );
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testCreateBlockException()
    {
        $this->model->createBlock('type', 'blockname', array());
    }

    public function testCreateBlockSuccess()
    {
        $blockMock = $this->getMockBuilder('Magento\Framework\View\Element\AbstractBlock')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->blockFactoryMock->expects($this->once())->method('createBlock')->will($this->returnValue($blockMock));

        $this->model->createBlock('type', 'blockname', array());
        $this->assertInstanceOf('Magento\Framework\View\Element\AbstractBlock', $this->model->getBlock('blockname'));
    }

    public function testGetUpdate()
    {
        $themeMock = $this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface');

        $this->themeResolverMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($themeMock));

        $this->processorFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('theme' => $themeMock))
            ->will($this->returnValue($this->processorMock));

        $this->assertEquals($this->processorMock, $this->model->getUpdate());
        $this->assertEquals($this->processorMock, $this->model->getUpdate());
    }

    public function testGenerateXml()
    {
        $themeMock = $this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface');

        $this->themeResolverMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($themeMock));

        $this->processorFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('theme' => $themeMock))
            ->will($this->returnValue($this->processorMock));

        $xmlString = '<?xml version="1.0"?><layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<some_update>123</some_update></layout>';
        $xml = simplexml_load_string($xmlString, 'Magento\Framework\View\Layout\Element');
        $this->processorMock->expects($this->once())
            ->method('asSimplexml')
            ->will($this->returnValue($xml));

        $this->structureMock->expects($this->once())
            ->method('importElements')
            ->with($this->equalTo([]))
            ->will($this->returnSelf());
        $this->assertSame($this->model, $this->model->generateXml());
        $this->assertSame('<some_update>123</some_update>', $this->model->getNode('some_update')->asXml());
    }

    /**
     * @param string $parentName
     * @param string $alias
     * @param string $name
     * @param bool $isBlock
     * @dataProvider getChildBlockDataProvider
     */
    public function testGetChildBlock($parentName, $alias, $name, $isBlock)
    {
        $this->structureMock->expects($this->once())
            ->method('getChildId')
            ->with($this->equalTo($parentName), $this->equalTo($alias))
            ->will($this->returnValue($name));
        $this->structureMock->expects($this->once())
            ->method('hasElement')
            ->with($this->equalTo($name))
            ->will($this->returnValue($isBlock));
        if ($isBlock) {
            $this->schStructureMock->expects($this->once())
                ->method('hasElement')
                ->with($this->equalTo($name))
                ->will($this->returnValue($isBlock));
            $this->structureMock->expects($this->once())
                ->method('getAttribute')
                ->with($this->equalTo($name), $this->equalTo('type'))
                ->will($this->returnValue(\Magento\Framework\View\Layout\Element::TYPE_BLOCK));
            $this->prepareGenerateBlock($name);
            $this->assertInstanceOf(
                'Magento\Framework\View\Element\AbstractBlock',
                $this->model->getChildBlock($parentName, $alias)
            );
        } else {
            $this->assertFalse($this->model->getChildBlock($parentName, $alias));
        }
    }

    /**
     * @return array
     */
    public function getChildBlockDataProvider()
    {
        return [
            ['parent_name', 'alias', 'block_name', true],
            ['parent_name', 'alias', 'block_name', false]
        ];
    }

    /**
     * @param string $name
     */
    protected function prepareGenerateBlock($name)
    {
        $blockClass = 'Magento\Framework\View\Element\Template';
        $xmlString = '<?xml version="1.0"?><block class="' . $blockClass . '"></block>';
        $xml = simplexml_load_string($xmlString, 'Magento\Framework\View\Layout\Element');
        $elementData = [\Magento\Framework\View\Layout\Element::TYPE_BLOCK, $xml, [], []];
        $this->schStructureMock->expects($this->once())
            ->method('getElement')
            ->with($this->equalTo($name))
            ->will($this->returnValue($elementData));
        $this->schStructureMock->expects($this->once())
            ->method('unsetElement')
            ->with($this->equalTo($name))
            ->will($this->returnSelf());
        $blockMock = $this->getMockBuilder('Magento\Framework\View\Element\AbstractBlock')
            ->setMethods(['setType', 'setNameInLayout', 'addData', 'setLayout'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $blockMock->expects($this->once())
            ->method('setType')
            ->with($this->equalTo(get_class($blockMock)))
            ->will($this->returnSelf());
        $blockMock->expects($this->once())
            ->method('setNameInLayout')
            ->with($this->equalTo($name))
            ->will($this->returnSelf());
        $blockMock->expects($this->once())
            ->method('addData')
            ->with($this->equalTo([]))
            ->will($this->returnSelf());
        $blockMock->expects($this->once())
            ->method('setLayout')
            ->with($this->equalTo($this->model))
            ->will($this->returnSelf());
        $this->blockFactoryMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo('Magento\Framework\View\Element\Template'), $this->equalTo(['data' => []]))
            ->will($this->returnValue($blockMock));
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo('core_layout_block_create_after'),
                $this->equalTo(['block' => $blockMock])
            )
            ->will($this->returnSelf());
    }
}
