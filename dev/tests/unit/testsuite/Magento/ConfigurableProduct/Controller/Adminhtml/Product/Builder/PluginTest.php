<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Controller\Adminhtml\Product\Builder;

class PluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\ConfigurableProduct\Controller\Adminhtml\Product\Builder\Plugin
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurableTypeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurableMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontendAttrMock;

    protected function setUp()
    {
        $this->productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', array('create'));
        $this->configurableTypeMock =
            $this->getMock('Magento\ConfigurableProduct\Model\Product\Type\Configurable', array(), array(), '', false);
        $this->invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $methods = array('setTypeId', 'getAttributes', 'addData', 'setWebsiteIds', '__wakeup');
        $this->productMock =
            $this->getMock('Magento\Catalog\Model\Product', $methods, array(), '', false);
        $this->invocationChainMock
            ->expects($this->once())
            ->method('proceed')
            ->with(array($this->requestMock))
            ->will($this->returnValue($this->productMock));
        $attributeMethods =
            array('getId', 'getFrontend', 'getAttributeCode', '__wakeup', 'setIsRequired', 'getIsUnique');
        $this->attributeMock
            = $this->getMock('Magento\Catalog\Model\Resource\Eav\Attribute', $attributeMethods, array(), '', false);
        $configMethods =
            array('setStoreId', 'getTypeInstance', 'getIdFieldName', 'getData',
                'getWebsiteIds', '__wakeup', 'load', 'setTypeId', 'getEditableAttributes');
        $this->configurableMock = $this->getMock(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable', $configMethods, array(), '', false);
        $this->frontendAttrMock =
            $this->getMock('Magento\Sales\Model\Resource\Quote\Address\Attribute\Frontend',
                array(), array(), '', false);
        $this->plugin = new \Magento\ConfigurableProduct\Controller\Adminhtml\Product\Builder\Plugin(
            $this->productFactoryMock,
            $this->configurableTypeMock
        );
    }

    public function testAroundBuild()
    {
        $this->requestMock->expects($this->once())->method('has')->with('attributes')->will($this->returnValue(true));
        $valueMap = array(
            array('attributes', null, array('attributes')),
            array('popup', null, true),
            array('required', null, '1,2'),
            array('product', null, 'product'),
            array('id', false, false),
            array('type', null, 'store_type'),
        );
        $this->requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($valueMap));
        $this->productMock
            ->expects($this->once())
            ->method('setTypeId')
            ->with(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
            ->will($this->returnSelf());
        $this->configurableTypeMock
            ->expects($this->once())
            ->method('setUsedProductAttributeIds')
            ->with(array('attributes'))
            ->will($this->returnSelf());
        $this->productMock
            ->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array($this->attributeMock)));
        $this->attributeMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->attributeMock->expects($this->once())->method('setIsRequired')->with(1)->will($this->returnSelf());
        $this->productFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->configurableMock));
        $this->configurableMock->expects($this->once())->method('setStoreId')->with(0)->will($this->returnSelf());
        $this->configurableMock
            ->expects($this->once())
            ->method('load')
            ->with('product')
            ->will($this->returnSelf());
        $this->configurableMock
            ->expects($this->once())
            ->method('setTypeId')
            ->with('store_type')
            ->will($this->returnSelf());
        $this->configurableMock->expects($this->once())->method('getTypeInstance')->will($this->returnSelf());
        $this->configurableMock
            ->expects($this->once())
            ->method('getEditableAttributes')
            ->with($this->configurableMock)
            ->will($this->returnValue(array($this->attributeMock)));
        $this->configurableMock
            ->expects($this->once())
            ->method('getIdFieldName')
            ->will($this->returnValue('fieldName'));
        $this->attributeMock->expects($this->once())->method('getIsUnique')->will($this->returnValue(false));
        $this->attributeMock
            ->expects($this->once())
            ->method('getFrontend')
            ->will($this->returnValue($this->frontendAttrMock));
        $this->frontendAttrMock->expects($this->once())->method('getInputType');
        $attributeCode = 'attribute_code';
        $this->attributeMock
            ->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));
        $this->configurableMock
            ->expects($this->once())
            ->method('getData')
            ->with($attributeCode)
            ->will($this->returnValue('attribute_data'));
        $this->productMock
            ->expects($this->once())
            ->method('addData')
            ->with(array($attributeCode => 'attribute_data'))
        ->will($this->returnSelf());
        $this->configurableMock
            ->expects($this->once())
            ->method('getWebsiteIds')
            ->will($this->returnValue('website_id'));
        $this->productMock
            ->expects($this->once())
            ->method('setWebsiteIds')
            ->with('website_id')
            ->will($this->returnSelf());

        $this->plugin->aroundBuild(array($this->requestMock), $this->invocationChainMock);
    }

    public function testAroundBuildWhenProductNotHaveAttributeAndRequiredParameters()
    {
        $valueMap = array(
            array('attributes', null, null),
            array('popup', null, false),
            array('product', null, 'product'),
            array('id', false, false),
        );
        $this->requestMock->expects($this->once())->method('has')->with('attributes')->will($this->returnValue(true));
        $this->requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($valueMap));
        $this->productMock
            ->expects($this->once())
            ->method('setTypeId')
            ->with(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $this->productMock->expects($this->never())->method('getAttributes');
        $this->productFactoryMock->expects($this->never())->method('create');
        $this->configurableMock->expects($this->never())->method('getTypeInstance');
        $this->attributeMock->expects($this->never())->method('getAttributeCode');
        $this->plugin->aroundBuild(array($this->requestMock), $this->invocationChainMock);
    }

    public function testAroundBuildWhenAttributesAreEmpty()
    {
        $valueMap = array(
            array('popup', null, false),
            array('product', null, 'product'),
            array('id', false, false),
        );
        $this->requestMock->expects($this->once())->method('has')->with('attributes')->will($this->returnValue(false));
        $this->requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($valueMap));
        $this->productMock->expects($this->never())->method('setTypeId');
        $this->productMock->expects($this->never())->method('getAttributes');
        $this->productFactoryMock->expects($this->never())->method('create');
        $this->configurableMock->expects($this->never())->method('getTypeInstance');
        $this->attributeMock->expects($this->never())->method('getAttributeCode');
        $this->plugin->aroundBuild(array($this->requestMock), $this->invocationChainMock);
    }

}
