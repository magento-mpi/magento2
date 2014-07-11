<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;


use Magento\TestFramework\Helper\ObjectManager;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\Link\ReadService
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
     * @var \Magento\Bundle\Service\V1\Data\Product\Link\MetadataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataConverter;

    /**
     * @var \Magento\Bundle\Model\Product\Type\Interceptor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productType;

    /**
     * @var \Magento\Bundle\Model\Resource\Option\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionCollection;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productType = $this->getMockBuilder('Magento\Bundle\Model\Product\Type\Interceptor')
            ->setMethods(['getOptionsCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->optionCollection = $this->getMockBuilder('Magento\Bundle\Model\Resource\Option\Collection')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getTypeInstance', 'getStoreId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataConverter = $this->getMockBuilder(
            'Magento\Bundle\Service\V1\Data\Product\Link\MetadataConverter'
        )
            ->setMethods(['createDataFromModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $helper->getObject(
            'Magento\Bundle\Service\V1\Product\Link\ReadService',
            [
                'productRepository' => $this->productRepository,
                'metadataConverter' => $this->metadataConverter
            ]
        );
    }

    public function testGetChildren()
    {
        $productSku = 'productSku';

        $this->getOptions();

        $this->productRepository->expects($this->any())->method('get')->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->model->getChildren($productSku);
    }

    public function getOptions()
    {
        $storeId = 2;
        $this->product->expects($this->once())->method('getTypeInstance')->will($this->returnValue($this->productType));
        $this->product->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

        $this->productType->expects($this->once())->method('setStoreFilter')
            ->with($this->equalTo($storeId), $this->equalTo($this->product));
        $this->productType->expects($this->once())->method('getOptionsCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->optionCollection));
    }
}
