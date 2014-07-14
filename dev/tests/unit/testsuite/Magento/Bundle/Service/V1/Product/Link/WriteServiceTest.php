<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;


use Magento\TestFramework\Helper\ObjectManager;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\Link\WriteService
     */
    private $model;

    /**
     * @var \Magento\Bundle\Model\Resource\Bundle|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bundleResource;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\Bundle\Model\Product\Type\Interceptor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productType;

    /**
     * @var \Magento\Bundle\Model\Resource\Option\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionCollection;

    /**
     * @var \Magento\Bundle\Model\Resource\Selection\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectionCollection;

    /**
     * @var \Magento\Bundle\Model\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    private $option;

    protected function setUp()
    {
        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getTypeInstance', 'getStoreId', 'getTypeId', 'getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $productRepository->expects($this->any())->method('get')->will($this->returnValue($this->product));

        $this->productType = $this->getMockBuilder('Magento\Bundle\Model\Product\Type\Interceptor')
            ->setMethods(['getOptionsCollection', 'setStoreFilter', 'getSelectionsCollection', 'getOptionsIds'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->option = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(['getSelections', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionCollection = $this->getMockBuilder('Magento\Bundle\Model\Resource\Option\Collection')
            ->setMethods(['appendSelections'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->selectionCollection = $this->getMockBuilder('Magento\Bundle\Model\Resource\Selection\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bundleResource = $this->getMockBuilder('\Magento\Bundle\Model\Resource\Bundle')
            ->setMethods(['dropAllUnneededSelections', 'saveProductRelations', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $bundleFactory = $this->getMockBuilder('\Magento\Bundle\Model\Resource\BundleFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $bundleFactory->expects($this->any())->method('create')->will($this->returnValue($this->bundleResource));

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\Bundle\Service\V1\Product\Link\WriteService',
            [
                'productRepository' => $productRepository,
                'bundleFactory' => $bundleFactory
            ]
        );
    }

    public function testRemoveChild()
    {
        $productSku = 'productSku';
        $optionId = 1;
        $childSku = 'childSku';

        $this->product
            ->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->getOptions();

        $selection = $this->getMockBuilder('\Magento\Bundle\Model\Selection')
            ->setMethods(['getSku', 'getOptionId', 'getSelectionId', 'getProductId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $selection->expects($this->any())->method('getSku')->will($this->returnValue($childSku));
        $selection->expects($this->any())->method('getOptionId')->will($this->returnValue($optionId));
        $selection->expects($this->any())->method('getSelectionId')->will($this->returnValue(55));
        $selection->expects($this->any())->method('getProductId')->will($this->returnValue(1));

        $this->option->expects($this->any())->method('getSelections')->will($this->returnValue([$selection]));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue(3));

        $this->bundleResource->expects($this->once())->method('dropAllUnneededSelections')->with(3, array());
        $this->bundleResource->expects($this->once())->method('saveProductRelations')->with(3, array());
        $this->assertTrue($this->model->removeChild($productSku, $optionId, $childSku));
    }

    /**
     * @expectedException \Magento\Webapi\Exception
     * @expectedExceptionCode 403
     */
    public function testRemoveChildForbidden()
    {
        $productSku = 'productSku';
        $optionId = 1;
        $childSku = 'childSku';

        $this->product
            ->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE));

        $this->model->removeChild($productSku, $optionId, $childSku);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveChildInvalidOptionId()
    {
        $productSku = 'productSku';
        $optionId = 1;
        $childSku = 'childSku';

        $this->product
            ->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->getOptions();

        $selection = $this->getMockBuilder('\Magento\Bundle\Model\Selection')
            ->setMethods(['getSku', 'getOptionId', 'getSelectionId', 'getProductId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $selection->expects($this->any())->method('getSku')->will($this->returnValue($childSku));
        $selection->expects($this->any())->method('getOptionId')->will($this->returnValue($optionId + 1));
        $selection->expects($this->any())->method('getSelectionId')->will($this->returnValue(55));
        $selection->expects($this->any())->method('getProductId')->will($this->returnValue(1));

        $this->option->expects($this->any())->method('getSelections')->will($this->returnValue([$selection]));
        $this->model->removeChild($productSku, $optionId, $childSku);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveChildInvalidChildSku()
    {
        $productSku = 'productSku';
        $optionId = 1;
        $childSku = 'childSku';

        $this->product
            ->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->getOptions();

        $selection = $this->getMockBuilder('\Magento\Bundle\Model\Selection')
            ->setMethods(['getSku', 'getOptionId', 'getSelectionId', 'getProductId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $selection->expects($this->any())->method('getSku')->will($this->returnValue($childSku . '_invalid'));
        $selection->expects($this->any())->method('getOptionId')->will($this->returnValue($optionId));
        $selection->expects($this->any())->method('getSelectionId')->will($this->returnValue(55));
        $selection->expects($this->any())->method('getProductId')->will($this->returnValue(1));

        $this->option->expects($this->any())->method('getSelections')->will($this->returnValue([$selection]));
        $this->model->removeChild($productSku, $optionId, $childSku);
    }

    public function getOptions()
    {
        $this->product->expects($this->any())->method('getTypeInstance')->will($this->returnValue($this->productType));
        $this->product->expects($this->once())->method('getStoreId')->will($this->returnValue(1));

        $this->productType->expects($this->once())->method('setStoreFilter');
        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->optionCollection));

        $this->productType->expects($this->once())->method('getOptionsIds')->with($this->equalTo($this->product))
            ->will($this->returnValue([1, 2, 3]));

        $this->productType->expects($this->once())->method('getSelectionsCollection')
            ->will($this->returnValue($this->selectionCollection));

        $this->optionCollection->expects($this->any())->method('appendSelections')
            ->with($this->equalTo($this->selectionCollection))
            ->will($this->returnValue([$this->option]));
    }
}
