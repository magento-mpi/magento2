<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product;

use Magento\Framework\ObjectManager\Helper\Composite as CompositeHelper;
use Magento\TestFramework\Helper\ObjectManager;

class ProductSaveProcessorCompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompositeHelper
     */
    protected $compositeHelperMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /** @var \Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite $saveProcessor */
    protected $saveProcessor;

    /** @var \Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite $saveProcessorMock */
    protected $saveProcessorMock;

    protected $processors;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->compositeHelperMock = $this->getMockBuilder('Magento\Framework\ObjectManager\Helper\Composite')
            ->disableOriginalConstructor()
            ->setMethods(['filterAndSortDeclaredComponents'])
            ->getMock();
        $this->compositeHelperMock
            ->expects($this->any())
            ->method('filterAndSortDeclaredComponents')
            ->will($this->returnArgument(0));
        $this->saveProcessorMock = $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'update', 'delete'])
            ->getMock();
        $this->processors = [
            [
                'sortOrder' => 10,
                'type' => $this->saveProcessorMock
            ]
        ];
        /** @var \Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite $saveProcessor */
        $this->saveProcessor = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite',
            ['compositeHelper' => $this->compositeHelperMock, 'saveProcessors' => $this->processors]
        );
    }

    public function testConstructor()
    {
        $saveProcessorMock = $this->createProductSaveProcessorCompositeMock();
        $processors = [
            [
                'sortOrder' => 10,
                'type' => $saveProcessorMock
            ]
        ];
        $model = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite',
            ['compositeHelper' => $this->compositeHelperMock, 'saveProcessors' => $processors]
        );
        $this->verifySaveProcessorIsAdded($model, $saveProcessorMock);
    }


    public function testCreate()
    {
        $expectedSku = 'test';
        $product = $this
            ->getMockBuilder('\Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productData = $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Data\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productData->expects($this->once())->method('getSku')->will($this->returnValue($expectedSku));

        $this->saveProcessorMock
            ->expects($this->once())
            ->method('create')
            ->with($product, $productData)
            ->will($this->returnValue($expectedSku));
        /** @var \Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite $saveProcessor */
        $saveProcessor = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite',
            ['compositeHelper' => $this->compositeHelperMock, 'saveProcessors' => $this->processors]
        );

        $actualSku = $saveProcessor->create($product, $productData);
        $this->assertEquals($expectedSku, $actualSku, 'Save processor is not created');
    }

    public function testDelete()
    {
        $expectedSku = 'test';
        $productData = $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Data\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $this->saveProcessorMock
            ->expects($this->once())
            ->method('delete')
            ->with($productData)
            ->will($this->returnValue($expectedSku));
        /** @var \Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite $saveProcessor */
        $saveProcessor = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite',
            ['compositeHelper' => $this->compositeHelperMock, 'saveProcessors' => $this->processors]
        );

        $this->assertTrue($saveProcessor->delete($productData), 'Save processor is deleted incorrectly');
    }

    public function testUpdate()
    {
        $expectedSku = 'test';
        $actualSku = '5';
        $productData = $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Data\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productData->expects($this->once())->method('getSku')->will($this->returnValue($expectedSku));

        $this->saveProcessorMock
            ->expects($this->once())
            ->method('update')
            ->with($actualSku, $productData)
            ->will($this->returnValue($expectedSku));

        $this->assertEquals(
            $expectedSku,
            $this->saveProcessor->update(5, $productData),
            'Save processor is updated incorrectly'
        );
    }

    /**
     * @param string|null $sku
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createProductSaveProcessorCompositeMock($sku = null)
    {
        $productSaveProcessorCompositeMock = $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'update'])
            ->getMock();
        if (!is_null($sku)) {
            $productSaveProcessorCompositeMock->expects($this->any())->method('create')->will($this->returnValue($sku));
            $productSaveProcessorCompositeMock->expects($this->any())->method('update')->will($this->returnValue($sku));
        }
        return $productSaveProcessorCompositeMock;
    }

    /**
     * @param ProductSaveProcessorInterface $model
     * @param ProductSaveProcessorInterface $saveProcessorMock
     */
    protected function verifySaveProcessorIsAdded($model, $saveProcessorMock)
    {
        $saveProcessor = new \ReflectionProperty(
            'Magento\Catalog\Service\V1\Product\ProductSaveProcessorComposite',
            'productSaveProcessors'
        );
        $saveProcessor->setAccessible(true);
        $values = $saveProcessor->getValue($model);
        $this->assertCount(1, $values, 'Save Processor is not registered.');
        $this->assertEquals($saveProcessorMock, $values[0], 'Save Processor is registered incorrectly.');
    }
}