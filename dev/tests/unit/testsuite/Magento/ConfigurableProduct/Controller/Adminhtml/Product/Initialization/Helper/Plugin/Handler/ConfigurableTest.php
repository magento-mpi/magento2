<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configurable
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypeMock;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-20297');
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product',
            array(
                'getConfigurableAttributesData', 'getTypeInstance', 'setConfigurableAttributesData', '__wakeup',
                'getTypeId'
            ),
            array(),
            '',
            false
        );
        $this->productTypeMock = $this->getMock(
            '\Magento\ConfigurableProduct\Model\Product\Type\Configurable', array(), array(), '', false
        );
        $this->productMock->expects($this->any())
            ->method('getTypeInstance')->will($this->returnValue($this->productTypeMock));
        $this->model = new Configurable();
    }

    public function testHandleWithNonConfigurableProductType()
    {
        $this->productMock->expects($this->once())->method('getTypeId')->will($this->returnValue('some product type'));
        $this->productMock->expects($this->never())->method('getConfigurableAttributesData');
        $this->model->handle($this->productMock);
    }

    public function testHandleWithoutOriginalProductAttributes()
    {
        $this->productMock->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE));
        $this->productTypeMock->expects($this->once())
            ->method('getConfigurableAttributesAsArray')
            ->with($this->productMock)
            ->will($this->returnValue(array()));

        $attributeData = array(
            array(
                'id' => 1,
                'values' => array(
                    array(
                        'value_index' => 0, 'pricing_value' => 10, 'is_percent' => 1,
                    )
                ),
            )
        );
        $this->productMock->expects($this->once())
            ->method('getConfigurableAttributesData')->will($this->returnValue($attributeData));

        $expected = array(
            array(
                'id' => 1,
                'values' => array(array('value_index' => 0, 'pricing_value' => 0, 'is_percent' => 0)),
            )
        );

        $this->productMock->expects($this->once())->method('setConfigurableAttributesData')->with($expected);
        $this->model->handle($this->productMock);
    }

    public function testHandleWithOriginalProductAttributes()
    {
        $originalAttributes = array(
            array(
                'id' => 1,
                'values' => array(
                    array('value_index' => 0, 'is_percent' => 10, 'pricing_value' => 50)
                ),
            ),
        );

        $this->productMock->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE));
        $this->productTypeMock->expects($this->once())
            ->method('getConfigurableAttributesAsArray')
            ->with($this->productMock)
            ->will($this->returnValue($originalAttributes));

        $attributeData = array(
            array(
                'id' => 1,
                'values' => array(
                    array('value_index' => 0, 'pricing_value' => 10, 'is_percent' => 1),
                    array('value_index' => 1, 'pricing_value' => 100, 'is_percent' => 200),
                ),
            )
        );
        $this->productMock->expects($this->once())
            ->method('getConfigurableAttributesData')->will($this->returnValue($attributeData));

        $expected = array(
            array(
                'id' => 1,
                'values' => array(
                    array('value_index' => 0, 'pricing_value' => 50, 'is_percent' => 10),
                    array('value_index' => 1, 'pricing_value' => 0, 'is_percent' => 0)
                ),
            )
        );

        $this->productMock->expects($this->once())->method('setConfigurableAttributesData')->with($expected);
        $this->model->handle($this->productMock);
    }
}
