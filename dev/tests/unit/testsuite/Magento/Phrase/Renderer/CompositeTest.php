<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_CompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Phrase_Renderer_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_rendererFactory;

    /**
     * @var Mage_Core_Model_App_State|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_state;

    /**
     * @var Mage_Core_Model_Logger|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    public function setUp()
    {
        $this->_rendererFactory = $this->getMock('Magento_Phrase_Renderer_Factory', array(), array(), '', false);
        $this->_state = $this->getMock('Mage_Core_Model_App_State', array(), array(), '', false);
        $this->_logger = $this->getMock('Mage_Core_Model_Logger', array(), array(), '', false);
    }

    /**
     * @param array $renderers
     * @return Magento_Phrase_Renderer_Composite
     */
    protected function _createComposite($renderers = array())
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        return $objectManagerHelper->getObject('Magento_Phrase_Renderer_Composite', array(
            'rendererFactory' => $this->_rendererFactory,
            'state' => $this->_state,
            'logger' => $this->_logger,
            'renderers' => $renderers,
        ));
    }

    public function testCreatingRenderersWhenCompositeCreating()
    {
        $this->_rendererFactory->expects($this->once())->method('create')->with('RenderClass')
            ->will($this->returnValue($this->getMockForAbstractClass('Magento_Phrase_RendererInterface')));

        $this->_createComposite(array(
            $this->getMock('Magento_Phrase_RendererInterface'),
            'RenderClass',
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWrongRendererExceptionWhenCompositeCreating()
    {
        $wrongRenderer = $this->getMock('WrongInterface');
        $this->getExpectedException('InvalidArgumentException', 'Wrong renderer ' . get_class($wrongRenderer));

        $this->_createComposite(array($wrongRenderer));
    }

    public function testCreatingRenderersWhenUsedAppend()
    {
        $this->_rendererFactory->expects($this->once())->method('create')->with('RenderClass')
            ->will($this->returnValue($this->getMock('Magento_Phrase_RendererInterface')));

        $this->_createComposite()->append('RenderClass');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMethod Wrong renderer WrongInterface
     */
    public function testWrongRendererExceptionWhenUsedAppend()
    {
        $this->_createComposite()->append($this->getMock('WrongInterface'));
    }

    public function testRender()
    {
        $text = 'some text';
        $arguments = array('arg1', 'arg2');
        $resultAfterFirst = 'rendered text first';
        $resultAfterSecond = 'rendered text second';

        $rendererFirst = $this->getMock('Magento_Phrase_RendererInterface');
        $rendererFirst->expects($this->once())->method('render')->with($text, $arguments)
            ->will($this->returnValue($resultAfterFirst));

        $rendererSecond = $this->getMock('Magento_Phrase_RendererInterface');
        $rendererSecond->expects($this->once())->method('render')->with($resultAfterFirst, $arguments)
            ->will($this->returnValue($resultAfterSecond));

        $this->assertEquals(
            $resultAfterSecond,
            $this->_createComposite(array($rendererFirst, $rendererSecond))->render($text, $arguments)
        );
    }

    public function testThatExceptionIsNotLoggedIfModeIsNotDeveloper()
    {
        $renderer = $this->getMock('Magento_Phrase_RendererInterface');
        $renderer->expects($this->once())->method('render')
            ->will($this->throwException(new Exception()));

        $this->_state->expects($this->once())->method('getMode')->will($this->returnValue('some-mode'));
        $this->_logger->expects($this->never())->method('logException');

        $this->_createComposite(array($renderer))->render('some text');
    }

    public function testThatExceptionIsLoggedIfModeIsDeveloper()
    {
        $exception = new Exception();
        $renderer = $this->getMock('Magento_Phrase_RendererInterface');
        $renderer->expects($this->once())->method('render')
            ->will($this->throwException($exception));

        $this->_state->expects($this->once())->method('getMode')
            ->will($this->returnValue(Mage_Core_Model_App_State::MODE_DEVELOPER));
        $this->_logger->expects($this->once())->method('logException')->with($exception);

        $this->_createComposite(array($renderer))->render('some text');
    }
}
