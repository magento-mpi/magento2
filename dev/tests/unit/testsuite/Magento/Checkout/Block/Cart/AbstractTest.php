<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @dataProvider getItemRendererDataProvider
     * @param string|null $type
     * @param string $expectedType
     */
    public function testGetItemRenderer($type, $expectedType)
    {
        $renderer = $this->getMock('Magento\View\Element\RendererList', array(), array(), '', false);

        $renderer->expects(
            $this->once()
        )->method(
            'getRenderer'
        )->with(
            $expectedType,
            AbstractCart::DEFAULT_TYPE
        )->will(
            $this->returnValue('rendererObject')
        );

        $layout = $this->getMock('Magento\Core\Model\Layout', array('getChildName', 'getBlock'), array(), '', false);

        $layout->expects($this->once())->method('getChildName')->will($this->returnValue('renderer.list'));

        $layout->expects(
            $this->once()
        )->method(
            'getBlock'
        )->with(
            'renderer.list'
        )->will(
            $this->returnValue($renderer)
        );

        /** @var $block \Magento\Sales\Block\Items\AbstractItems */
        $block = $this->_objectManager->getObject(
            'Magento\Checkout\Block\Cart\AbstractCart',
            array(
                'context' => $this->_objectManager->getObject(
                    'Magento\Backend\Block\Template\Context',
                    array('layout' => $layout)
                )
            )
        );

        $this->assertSame('rendererObject', $block->getItemRenderer($type));
    }

    /**
     * @return array
     */
    public function getItemRendererDataProvider()
    {
        return array(array(null, AbstractCart::DEFAULT_TYPE), array('some-type', 'some-type'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Renderer list for block "" is not defined
     */
    public function testGetItemRendererThrowsExceptionForNonexistentRenderer()
    {
        $layout = $this->getMock('Magento\Core\Model\Layout', array('getChildName', 'getBlock'), array(), '', false);
        $layout->expects($this->once())->method('getChildName')->will($this->returnValue(null));

        /** @var $block \Magento\Checkout\Block\Cart\AbstractCart */
        $block = $this->_objectManager->getObject(
            'Magento\Checkout\Block\Cart\AbstractCart',
            array(
                'context' => $this->_objectManager->getObject(
                    'Magento\Backend\Block\Template\Context',
                    array('layout' => $layout)
                )
            )
        );

        $block->getItemRenderer('some-type');
    }
}
