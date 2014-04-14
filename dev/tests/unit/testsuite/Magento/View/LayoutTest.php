<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Layout
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockFactoryMock;

    /**
     * @var \Magento\View\Layout\ProcessorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processorFactoryMock;

    /**
     * @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appStateMock;

    /**
     * @var \Magento\View\Design\Theme\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeResolverMock;

    /**
     * @var \Magento\Core\Model\Layout\Merge|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processorMock;

    protected function setUp()
    {
        $this->_structureMock = $this->getMockBuilder(
            'Magento\Framework\Data\Structure'
        )->setMethods(
            array('createElement')
        )->disableOriginalConstructor()->getMock();
        $this->_blockFactoryMock = $this->getMockBuilder(
            'Magento\View\Element\BlockFactory'
        )->setMethods(
            array('createBlock')
        )->disableOriginalConstructor()->getMock();
        $this->processorFactoryMock = $this->getMock(
            'Magento\View\Layout\ProcessorFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->appStateMock = $this->getMock(
            'Magento\Framework\App\State',
            [],
            [],
            '',
            false
        );
        $this->themeResolverMock = $this->getMockForAbstractClass('Magento\View\Design\Theme\ResolverInterface');
        $this->processorMock = $this->getMock(
            'Magento\Core\Model\Layout\Merge',
            ['__destruct'],
            [],
            '',
            false
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\View\Layout',
            array(
                'structure' => $this->_structureMock,
                'blockFactory' => $this->_blockFactoryMock,
                'themeResolver' => $this->themeResolverMock,
                'processorFactory' => $this->processorFactoryMock,
                'appState' => $this->appStateMock,
            )
        );
    }

    /**
     * @expectedException \Magento\Model\Exception
     */
    public function testCreateBlockException()
    {
        $this->_model->createBlock('type', 'blockname', array());
    }

    public function testCreateBlockSuccess()
    {
        $blockMock = $this->getMockBuilder(
            'Magento\View\Element\AbstractBlock'
        )->disableOriginalConstructor()->getMockForAbstractClass();
        $this->_blockFactoryMock->expects($this->once())->method('createBlock')->will($this->returnValue($blockMock));

        $this->_model->createBlock('type', 'blockname', array());
        $this->assertInstanceOf('Magento\View\Element\AbstractBlock', $this->_model->getBlock('blockname'));
    }

    public function testGetUpdate()
    {
        $themeMock = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');

        $this->themeResolverMock->expects(
            $this->once()
        )->method(
            'get'
        )->will(
            $this->returnValue($themeMock)
        );

        $this->processorFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            array('theme' => $themeMock)
        )->will(
            $this->returnValue($this->processorMock)
        );

        $this->assertEquals($this->processorMock, $this->_model->getUpdate());
        $this->assertEquals($this->processorMock, $this->_model->getUpdate());
    }
}
