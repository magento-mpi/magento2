<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Product\TypeTransitionManager\Plugin;

class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\TypeTransitionManager\Plugin\Configurable
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->model = new Configurable($this->requestMock);
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('setTypeId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->subjectMock = $this->getMock(
            'Magento\Catalog\Model\Product\TypeTransitionManager',
            array(),
            array(),
            '',
            false
        );
        $this->closureMock = function () {
            return 'Expected';
        };
    }

    public function testAroundProcessProductWithProductThatCanBeTransformedToConfigurable()
    {
        $this->requestMock->expects(
            $this->any()
        )->method(
            'getParam'
        )->with(
            'attributes'
        )->will(
            $this->returnValue('not_empty_attribute_data')
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'setTypeId'
        )->with(
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
        );
        $this->model->aroundProcessProduct($this->subjectMock, $this->closureMock, $this->productMock);
    }

    public function testAroundProcessProductWithProductThatCannotBeTransformedToConfigurable()
    {
        $this->requestMock->expects(
            $this->any()
        )->method(
            'getParam'
        )->with(
            'attributes'
        )->will(
            $this->returnValue(null)
        );
        $this->productMock->expects($this->never())->method('setTypeId');
        $this->model->aroundProcessProduct($this->subjectMock, $this->closureMock, $this->productMock);
    }
}
