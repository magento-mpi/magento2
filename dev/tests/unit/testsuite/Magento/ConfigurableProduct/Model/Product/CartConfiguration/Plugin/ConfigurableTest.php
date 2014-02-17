<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Product\CartConfiguration\Plugin;

class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\CartConfiguration\Plugin\Configurable
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->model = new Configurable();
    }

    public function testAroundIsProductConfiguredChecksThatSuperAttributeIsSetWhenProductIsConfigurable()
    {
        $config = array('super_attribute' => 'valid_value');
        $this->productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));
        $this->invocationChainMock->expects($this->never())->method('proceed');
        $this->assertEquals(
            true,
            $this->model->aroundIsProductConfigured(array($this->productMock, $config), $this->invocationChainMock)
        );
    }

    public function testAroundIsProductConfiguredProceedsChainInvocationWhenProductIsNotConfigurable()
    {
        $config = array('super_group' => 'valid_value');
        $this->productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue('custom_product_type'));
        $this->invocationChainMock->expects($this->once())
            ->method('proceed')
            ->with(array($this->productMock, $config));
        $this->model->aroundIsProductConfigured(array($this->productMock, $config), $this->invocationChainMock);
    }
}
