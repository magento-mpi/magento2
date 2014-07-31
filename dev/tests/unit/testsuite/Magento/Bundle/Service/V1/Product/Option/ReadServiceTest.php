<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\TestFramework\Helper\ObjectManager;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\ReadService
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\OptionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionConverter;

    /**
     * @var \Magento\Bundle\Model\Product\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productType;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\Bundle\Model\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionModel;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    private $option;

    /**
     * @var \Magento\Bundle\Model\Resource\Option\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionCollection;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productType = $this->getMockBuilder('Magento\Bundle\Model\Product\Type')
            ->setMethods(['getOptionsCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getTypeId', 'getTypeInstance'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionModel = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(['__wakeup', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionCollection = $this->getMockBuilder('Magento\Bundle\Model\Resource\Option\Collection')
            ->setMethods(['setIdFilter', 'getFirstItem'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionConverter = $this->getMockBuilder('\Magento\Bundle\Service\V1\Data\Product\OptionConverter')
            ->setMethods(['createDataFromModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->option = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Option')
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\Bundle\Service\V1\Product\Option\ReadService',
            [
                'optionConverter' => $this->optionConverter,
                'productRepository' => $this->productRepository,
                'type' => $this->productType
            ]
        );
    }

    public function testGet()
    {
        $productSku = 'oneSku';
        $optionId = 3;

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->optionCollection->expects($this->once())->method('setIdFilter')
            ->with($this->equalTo($optionId));
        $this->optionCollection->expects($this->once())->method('getFirstItem')
            ->will($this->returnValue($this->optionModel));

        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->optionCollection));

        $this->optionConverter->expects($this->once())->method('createDataFromModel')
            ->with($this->equalTo($this->optionModel), $this->equalTo($this->product))
            ->will($this->returnValue($this->option));

        $this->optionModel->expects($this->once())->method('getId')->will($this->returnValue($optionId));

        $this->assertEquals($this->option, $this->model->get($productSku, $optionId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetNoSuchEntityException()
    {
        $productSku = 'oneSku';
        $optionId = 3;

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->optionCollection->expects($this->once())->method('setIdFilter')
            ->with($this->equalTo($optionId));
        $this->optionCollection->expects($this->once())->method('getFirstItem')
            ->will($this->returnValue($this->optionModel));

        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->optionCollection));

        $this->optionModel->expects($this->once())->method('getId');

        $this->model->get($productSku, $optionId);
    }

    public function testGetList()
    {
        $productSku = 'oneSku';

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));

        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue([$this->optionModel]));

        $this->optionConverter->expects($this->once())->method('createDataFromModel')
            ->with($this->equalTo($this->optionModel), $this->equalTo($this->product))
            ->will($this->returnValue($this->option));

        $this->assertEquals([$this->option], $this->model->getList($productSku));
    }

    /**
     * @expectedException \Magento\Webapi\Exception
     * @expectedExceptionCode 403
     */
    public function testGetListWebApiException()
    {
        $productSku = 'oneSku';

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE));

        $this->model->getList($productSku);
    }
}
