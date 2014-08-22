<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Catalog\Model\Product\Type as ProductType;

class BundleProductSaveProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\BundleProductSaveProcessor
     */
    private $saveProcessor;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productData;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productLink1;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productLink2;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productLink3;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productOption1;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productOption2;

    /**
     * @var \Magento\Bundle\Service\V1\Product\Link\WriteService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $linkWriteService;

    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\WriteService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionWriteService;

    /**
     * @var \Magento\Bundle\Service\V1\Product\Link\ReadService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $linkReadService;

    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\ReadService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionReadService;

    /**
     * @var \Magento\Catalog\Service\V1\Data\ProductBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productBuilder;

    protected function setup()
    {
        $helper = new ObjectManager($this);

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productData = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Product')
            ->setMethods(['getSku', 'getTypeId', '__wakeup','getCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getSku', 'getTypeId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productLink1 = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Link\Metadata')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getSku'])
            ->getMock();
        $this->productLink1->expects($this->any())
            ->method('getSku')
            ->will($this->returnValue('productLink1Sku'));
        $this->productLink2 = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Link\Metadata')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getSku'])
            ->getMock();
        $this->productLink2->expects($this->any())
            ->method('getSku')
            ->will($this->returnValue('productLink2Sku'));
        $this->productLink3 = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Link\Metadata')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getSku'])
            ->getMock();
        $this->productLink3->expects($this->any())
            ->method('getSku')
            ->will($this->returnValue('productLink3Sku'));

        $this->productOption1 = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Option')
            ->disableOriginalConstructor()
            ->getMock();
        $this->productOption1->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('productOption1Id'));
        $this->productOption2 = $this->getMockBuilder('Magento\Bundle\Service\V1\Data\Product\Option')
            ->disableOriginalConstructor()
            ->getMock();
        $this->productOption2->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('productOption2Id'));

        $this->linkWriteService = $this->getMockBuilder('Magento\Bundle\Service\V1\Product\Link\WriteService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionWriteService = $this->getMockBuilder('Magento\Bundle\Service\V1\Product\Option\WriteService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->linkReadService = $this->getMockBuilder('Magento\Bundle\Service\V1\Product\Link\ReadService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionReadService = $this->getMockBuilder('Magento\Bundle\Service\V1\Product\Option\ReadService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productBuilder = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\ProductBuilder')
            ->setMethods(['setCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->saveProcessor = $helper->getObject(
            'Magento\Bundle\Service\V1\Product\BundleProductSaveProcessor',
            [
                'linkWriteService' => $this->linkWriteService,
                'optionWriteService' => $this->optionWriteService,
                'linkReadService' => $this->linkReadService,
                'optionReadService' => $this->optionReadService,
                'productRepository' => $this->productRepository,
            ]
        );
    }

    public function testCreate()
    {
        $productSku = 'sku';
        $productLinks = [$this->productLink1, $this->productLink2];
        $productOptions = [$this->productOption1, $this->productOption2];

        $this->productData->expects($this->once())
            ->method('getSku')
            ->will($this->returnValue($productSku));
        $this->productData->expects($this->at(1))
            ->method('getCustomAttribute')
            ->with('bundle_product_links')
            ->will($this->returnValue($productLinks));
        $this->productData->expects($this->at(2))
            ->method('getCustomAttribute')
            ->with('bundle_product_options')
            ->will($this->returnValue($productOptions));

        $this->linkWriteService->expects($this->at(0))
            ->method('addChild')
            ->with($productSku, $this->productLink1)
            ->will($this->returnValue(1));
        $this->linkWriteService->expects($this->at(1))
            ->method('addChild')
            ->with($productSku, $this->productLink2)
            ->will($this->returnValue(2));

        $this->optionWriteService->expects($this->at(0))
            ->method('add')
            ->with($productSku, $this->productOption1)
            ->will($this->returnValue(1));
        $this->optionWriteService->expects($this->at(1))
            ->method('add')
            ->with($productSku, $this->productOption2)
            ->will($this->returnValue(2));

        $this->assertTrue($this->saveProcessor->create($this->product, $this->productData));
    }

    public function testUpdate()
    {
        $product1Id = '1';
        $product1Sku = 'sku1';
        $productLink1Sku = 'productLink1Sku';
        $productLink2Sku = 'productLink2Sku';
        $productLink3Sku = 'productLink3Sku';
        $product1Links = [$this->productLink1, $this->productLink2];
        $productOption1Id = 'productOption1Id';
        $productOption2Id = 'productOption2Id';
        $product1Options = [$this->productOption1, $this->productOption2];

        $product2Links = [$this->productLink1, $this->productLink2, $this->productLink3];
        $product2Options = [$this->productOption1];

        $this->productRepository->expects($this->once())
            ->method('getById')
            ->with($product1Id, true)
            ->will($this->returnValue($this->product));
        $this->product->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(ProductType::TYPE_BUNDLE));
        $this->product->expects($this->once())
            ->method('getSku')
            ->will($this->returnValue($product1Sku));

        $this->linkReadService->expects($this->once())
            ->method('getChildren')
            ->with($product1Id)
            ->will($this->returnValue($product1Links));
        $this->productData->expects($this->at(0))
            ->method('getCustomAttribute')
            ->with('bundle_product_links')
            ->will($this->returnValue($product2Links));
        $this->productLink1->expects($this->any())
            ->method('getSku')
            ->will($this->returnValue($productLink1Sku));
        $this->productLink2->expects($this->any())
            ->method('getSku')
            ->will($this->returnValue($productLink2Sku));
        $this->productLink3->expects($this->any())
            ->method('getSku')
            ->will($this->returnValue($productLink3Sku));
        $this->linkWriteService->expects($this->once())
            ->method('addChild')
            ->with($product1Sku, $this->productLink3)
            ->will($this->returnValue(1));

        $this->optionReadService->expects($this->once())
            ->method('getList')
            ->with($product1Sku)
            ->will($this->returnValue($product1Options));
        $this->productData->expects($this->at(1))
            ->method('getCustomAttribute')
            ->with('bundle_product_options')
            ->will($this->returnValue($product2Options));
        $this->productOption1->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productOption1Id));
        $this->productOption2->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productOption2Id));
        $this->optionWriteService->expects($this->once())
            ->method('remove')
            ->with($product1Sku, $productOption2Id)
            ->will($this->returnValue(1));

        $this->assertTrue($this->saveProcessor->update($product1Id, $this->productData));

    }

    public function testDelete()
    {
        $productSku = 'sku1';
        $productLinks = [$this->productLink1, $this->productLink2, $this->productLink3];
        $productOptions = [$this->productOption1];
        $productOption1Id = 'productOption1Id';
        $productLink1Sku = 'productLink1Sku';
        $productLink2Sku = 'productLink2Sku';

        $this->productData->expects($this->once())
            ->method('getSku')
            ->will($this->returnValue($productSku));
        $this->productData->expects($this->at(1))
            ->method('getCustomAttribute')
            ->with('bundle_product_links')
            ->will($this->returnValue($productLinks));
        $this->productData->expects($this->at(2))
            ->method('getCustomAttribute')
            ->with('bundle_product_options')
            ->will($this->returnValue($productOptions));
        $this->linkWriteService->expects($this->at(0))
            ->method('removeChild')
            ->with($productSku, 'dummy', $productLink1Sku)
            ->will($this->returnValue(1));
        $this->linkWriteService->expects($this->at(1))
            ->method('removeChild')
            ->with($productSku, 'dummy', $productLink2Sku)
            ->will($this->returnValue(1));
        $this->optionWriteService->expects($this->once())
            ->method('remove')
            ->with($productSku, $productOption1Id)
            ->will($this->returnValue(1));
        $this->assertTrue($this->saveProcessor->delete($this->productData));

    }

} 