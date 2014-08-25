<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product;

use Magento\TestFramework\Helper\ObjectManager;

class BundleProductLoadProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\BundleProductLoadProcessor
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\ReadService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionReadService;

    /**
     * @var \Magento\Bundle\Service\V1\Product\Link\ReadService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $linkReadService;

    /**
     * @var \Magento\Catalog\Service\V1\Data\ProductBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productBuilder;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['getByProductId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getSku', 'getTypeId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionReadService = $this->getMockBuilder('Magento\Bundle\Service\V1\Product\Option\ReadService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->linkReadService = $this->getMockBuilder('Magento\Bundle\Service\V1\Product\Link\ReadService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productBuilder = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\ProductBuilder')
            ->setMethods(['setCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $helper->getObject(
            'Magento\Bundle\Service\V1\Product\BundleProductLoadProcessor',
            [
                'optionReadService' => $this->optionReadService,
                'linkReadService'   => $this->linkReadService,
                'productRepository' => $this->productRepository,
            ]
        );
    }

    public function testLoadNotBundleProduct()
    {
        $productId = 'test_id';

        $this->productRepository->expects($this->once())
            ->method('getByProductId')
            ->with($productId)
            ->will($this->returnValue($this->product));
        $this->product->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE));

        $this->model->load($productId, $this->productBuilder);
    }

    public function testLoadBundleProduct()
    {
        $productId = 'test_id';
        $productSku = 'test_sku';

        $this->productRepository->expects($this->once())
            ->method('getByProductId')
            ->with($productId)
            ->will($this->returnValue($this->product));
        $this->product->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE));
        $this->product->expects($this->once())
            ->method('getSku')
            ->will($this->returnValue($productSku));

        $optionCustomAttributeValue = ['a', 'b'];
        $this->optionReadService->expects($this->once())
            ->method('getList')
            ->with($productSku)
            ->will($this->returnValue($optionCustomAttributeValue));
        $this->productBuilder->expects($this->at(0))
            ->method('setCustomAttribute')
            ->with('bundle_product_options', $optionCustomAttributeValue);

        $linkCustomAttributeValue = ['x', 'y'];
        $this->linkReadService->expects($this->once())
            ->method('getChildren')
            ->with($productId)
            ->will($this->returnValue($linkCustomAttributeValue));
        $this->productBuilder->expects($this->at(1))
            ->method('setCustomAttribute')
            ->with('bundle_product_links', $linkCustomAttributeValue);

        $this->model->load($productId, $this->productBuilder);
    }
}
