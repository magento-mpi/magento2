<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Product\Validator;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Validator\Plugin
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var array
     */
    protected $proceedResult = array(1, 2, 3);

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    protected function setUp()
    {
        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\Manager', array(), array(), '', false);
        $this->productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->coreHelperMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->requestMock = $this->getMock(
            'Magento\Framework\App\Request\Http',
            array('getPost', 'getParam', '__wakeup'),
            array(),
            '',
            false
        );
        $this->responseMock = $this->getMock(
            'Magento\Framework\Object',
            array('setError', 'setMessage', 'setAttributes'),
            array(),
            '',
            false
        );
        $this->arguments = array($this->productMock, $this->requestMock, $this->responseMock);
        $proceedResult = $this->proceedResult;
        $this->closureMock = function () use ($proceedResult) {
            return $proceedResult;
        };
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Product\Validator', array(), array(), '', false);
        $this->plugin = new \Magento\ConfigurableProduct\Model\Product\Validator\Plugin(
            $this->eventManagerMock,
            $this->productFactoryMock,
            $this->coreHelperMock
        );
    }

    public function testAroundValidateWithVariationsValid()
    {
        $matrix = array('products');

        $plugin = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Validator\Plugin',
            array('_validateProductVariations'),
            array($this->eventManagerMock, $this->productFactoryMock, $this->coreHelperMock)
        );

        $plugin->expects(
            $this->once()
        )->method(
            '_validateProductVariations'
        )->with(
            $this->productMock,
            $matrix,
            $this->requestMock
        )->will(
            $this->returnValue(null)
        );

        $this->requestMock->expects(
            $this->once()
        )->method(
            'getPost'
        )->with(
            'variations-matrix'
        )->will(
            $this->returnValue($matrix)
        );

        $this->responseMock->expects($this->never())->method('setError');

        $this->assertEquals(
            $this->proceedResult,
            $plugin->aroundValidate(
                $this->subjectMock,
                $this->closureMock,
                $this->productMock,
                $this->requestMock,
                $this->responseMock
            )
        );
    }

    public function testAroundValidateWithVariationsInvalid()
    {
        $matrix = array('products');

        $plugin = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Validator\Plugin',
            array('_validateProductVariations'),
            array($this->eventManagerMock, $this->productFactoryMock, $this->coreHelperMock)
        );

        $plugin->expects(
            $this->once()
        )->method(
            '_validateProductVariations'
        )->with(
            $this->productMock,
            $matrix,
            $this->requestMock
        )->will(
            $this->returnValue(true)
        );

        $this->requestMock->expects(
            $this->once()
        )->method(
            'getPost'
        )->with(
            'variations-matrix'
        )->will(
            $this->returnValue($matrix)
        );

        $this->responseMock->expects($this->once())->method('setError')->with(true)->will($this->returnSelf());
        $this->responseMock->expects($this->once())->method('setMessage')->will($this->returnSelf());
        $this->responseMock->expects($this->once())->method('setAttributes')->will($this->returnSelf());
        $this->assertEquals(
            $this->proceedResult,
            $plugin->aroundValidate(
                $this->subjectMock,
                $this->closureMock,
                $this->productMock,
                $this->requestMock,
                $this->responseMock
            )
        );
    }

    public function testAroundValidateIfVariationsNotExist()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getPost'
        )->with(
            'variations-matrix'
        )->will(
            $this->returnValue(null)
        );
        $this->eventManagerMock->expects($this->never())->method('dispatch');
        $this->plugin->aroundValidate(
            $this->subjectMock,
            $this->closureMock,
            $this->productMock,
            $this->requestMock,
            $this->responseMock
        );
    }
}
