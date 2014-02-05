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
    protected $invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\TypeTransitionManager\Plugin\Configurable
     */
    protected $model;

    protected function setUp()
    {
        $this->requestMock = $this->getMock(
            'Magento\App\Request\Http',
            array(),
            array(),
            '',
            false
        );
        $this->model = new Configurable($this->requestMock);
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('setTypeId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
    }

    public function testAroundProcessProductWithProductThatCanBeTransformedToConfigurable()
    {
        $this->requestMock->expects($this->any())->method('getParam')->with('attributes')
            ->will($this->returnValue('not_empty_attribute_data'));
        $this->productMock->expects($this->once())->method('setTypeId')
            ->with(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE);
        $this->invocationChainMock->expects($this->never())->method('proceed');
        $this->model->aroundProcessProduct(array($this->productMock), $this->invocationChainMock);
    }

    public function testAroundProcessProductWithProductThatCannotBeTransformedToConfigurable() {
        $this->requestMock->expects($this->any())->method('getParam')->with('attributes')
            ->will($this->returnValue(null));
        $this->productMock->expects($this->never())->method('setTypeId');
        $arguments = array($this->productMock);
        $this->invocationChainMock->expects($this->once())->method('proceed')->with($arguments);
        $this->model->aroundProcessProduct($arguments, $this->invocationChainMock);
    }
}
