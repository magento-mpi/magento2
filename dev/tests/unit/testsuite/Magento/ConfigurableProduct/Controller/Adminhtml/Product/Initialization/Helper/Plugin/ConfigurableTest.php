<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Controller\Adminhtml\Product\Initialization\Helper\Plugin;


class ConfigurableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\ConfigurableProduct\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Configurable
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->productTypeMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable', array(), array(), '', false
        );
        $this->requestMock = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $methods = array('setNewVariationsAttributeSetId', 'setAssociatedProductIds',
            'setCanSaveConfigurableAttributes', '__wakeup');
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', $methods, array(), '', false);
        $this->plugin = new Configurable(
            $this->productTypeMock,
            $this->requestMock
        );
    }

    public function testAfterInitializeIfAttributesNotEmptyAndActionNameNotGenerateVariations()
    {
        $associatedProductIds = array('key' => 'value');
        $generatedProductIds = array('key_one' => 'value_one');
        $expectedArray = array('key' => 'value', 'key_one' => 'value_one');
        $attributes = array('key' => 'value');
        $postValue = 'postValue';
        $valueMap = array(
            array('new-variations-attribute-set-id', null, $postValue),
            array('associated_product_ids', array(), $associatedProductIds),
            array('variations-matrix', array(), $postValue),
            array('affect_configurable_product_attributes', null, $postValue)
        );
        $this->requestMock->expects($this->any())->method('getPost')->will($this->returnValueMap($valueMap));
        $this->requestMock
            ->expects($this->once())
            ->method('getParam')
            ->with('attributes')
            ->will($this->returnValue($attributes));
        $this->productTypeMock
            ->expects($this->once())
            ->method('setUsedProductAttributeIds')
            ->with($attributes, $this->productMock);
        $this->productMock->expects($this->once())->method('setNewVariationsAttributeSetId')->with($postValue);
        $this->requestMock->expects($this->once())->method('getActionName')->will($this->returnValue('action_name'));
        $this->productTypeMock
            ->expects($this->once())
            ->method('generateSimpleProducts')
            ->with($this->productMock, $postValue)->will($this->returnValue($generatedProductIds));
        $this->productMock->expects($this->once())->method('setAssociatedProductIds')->with($expectedArray);
        $this->productMock->expects($this->once())->method('setCanSaveConfigurableAttributes')->with(true);
        $this->plugin->afterInitialize($this->productMock);
    }

    public function testAfterInitializeIfAttributesNotEmptyAndActionNameGenerateVariations()
    {
        $associatedProductIds = array('key' => 'value');
        $attributes = array('key' => 'value');
        $postValue = 'postValue';
        $valueMap = array(
            array('new-variations-attribute-set-id', null, $postValue),
            array('associated_product_ids', array(), $associatedProductIds),
            array('variations-matrix', array(), $postValue),
            array('affect_configurable_product_attributes', null, $postValue)
        );
        $this->requestMock->expects($this->any())->method('getPost')->will($this->returnValueMap($valueMap));
        $this->requestMock
            ->expects($this->once())
            ->method('getParam')
            ->with('attributes')
            ->will($this->returnValue($attributes));
        $this->productTypeMock
            ->expects($this->once())
            ->method('setUsedProductAttributeIds')
            ->with($attributes, $this->productMock);
        $this->productMock->expects($this->once())->method('setNewVariationsAttributeSetId')->with($postValue);
        $this->requestMock
            ->expects($this->once())
            ->method('getActionName')
            ->will($this->returnValue('generateVariations'));
        $this->productTypeMock
            ->expects($this->never())
            ->method('generateSimpleProducts');
        $this->productMock->expects($this->once())->method('setAssociatedProductIds')->with($associatedProductIds);
        $this->productMock->expects($this->once())->method('setCanSaveConfigurableAttributes')->with(true);
        $this->plugin->afterInitialize($this->productMock);
    }

    public function testAfterInitializeIfAttributesEmpty()
    {
        $this->requestMock
            ->expects($this->once())
            ->method('getParam')
            ->with('attributes')
            ->will($this->returnValue(array()));
        $this->productTypeMock->expects($this->never())->method('setUsedProductAttributeIds');
        $this->requestMock->expects($this->never())->method('getPost');
        $this->productTypeMock->expects($this->never())->method('generateSimpleProducts');
        $this->plugin->afterInitialize($this->productMock);
    }
}
