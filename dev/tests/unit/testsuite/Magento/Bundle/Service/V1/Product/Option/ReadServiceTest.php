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
     * @var \Magento\Bundle\Service\V1\Data\Option\MetadataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataConverter;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getTypeId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            'Magento\Bundle\Service\V1\Product\Option\ReadService',
            ['metadataConverter' => $this->metadataConverter, 'productRepository' => $this->productRepository]
        );
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
