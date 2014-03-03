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
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_invFramework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->subjectMock =
            $this->getMock('Magento\Catalog\Model\Product\CartConfiguration', array(), array(), '', false);
        $this->model = new Configurable();
    }

    public function testAroundIsProductConfiguredChecksThatSuperAttributeIsSetWhenProductIsConfigurable()
    {
        $config = array('super_attribute' => 'valid_value');
        $this->productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));
        $this->assertEquals(
            true,
            $this->model->aroundIsProductConfigured($this->subjectMock, $this->closureMock, $this->productMock, $config)
        );
    }

    public function testAroundIsProductConfiguredWhenProductIsNotConfigurable()
    {
        $config = array('super_group' => 'valid_value');
        $this->productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue('custom_product_type'));
        $this->assertEquals('Expected',
        $this->model->aroundIsProductConfigured($this->subjectMock, $this->closureMock, $this->productMock, $config));
    }
}
