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

class ProductLoadProcessorCompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompositeHelper
     */
    protected $compositeHelperMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /** @var \Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite $loadProcessor */
    protected $loadProcessor;

    /** @var \Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite $loadProcessorMock */
    protected $loadProcessorMock;

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
        $this->loadProcessorMock = $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite')
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();
        $this->processors = [
            [
                'sortOrder' => 10,
                'type' => $this->loadProcessorMock
            ]
        ];
        $this->loadProcessor = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite',
            ['compositeHelper' => $this->compositeHelperMock, 'loadProcessors' => $this->processors]
        );
    }

    public function testConstructor()
    {
        $loadProcessorMock = $this->createProductLoadProcessorCompositeMock();
        $processors = [
            [
                'sortOrder' => 10,
                'type' => $loadProcessorMock
            ]
        ];
        $compositeProcessor = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite',
            ['compositeHelper' => $this->compositeHelperMock, 'loadProcessors' => $processors]
        );
        $this->verifyLoadProcessorIsAdded($compositeProcessor, $loadProcessorMock);
    }

    public function testLoad()
    {
        $productSku = '5';
        $productDataBuilder = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\ProductBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->loadProcessorMock
            ->expects($this->once())
            ->method('load')
            ->with($productSku, $productDataBuilder);
        $loadProcessor = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite',
            ['compositeHelper' => $this->compositeHelperMock, 'loadProcessors' => $this->processors]
        );
        $loadProcessor->load($productSku, $productDataBuilder);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createProductLoadProcessorCompositeMock()
    {
        $productLoadProcessorCompositeMock = $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite')
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();
        $productLoadProcessorCompositeMock->expects($this->any())->method('load');
        return $productLoadProcessorCompositeMock;
    }

    /**
     * @param ProductLoadProcessorInterface $compositeProcessor
     * @param ProductLoadProcessorInterface $loadProcessorMock
     */
    protected function verifyLoadProcessorIsAdded($compositeProcessor, $loadProcessorMock)
    {
        $loadProcessor = new \ReflectionProperty(
            'Magento\Catalog\Service\V1\Product\ProductLoadProcessorComposite',
            'productLoadProcessors'
        );
        $loadProcessor->setAccessible(true);
        $values = $loadProcessor->getValue($compositeProcessor);
        $this->assertCount(1, $values, 'Load Processor is not registered.');
        $this->assertEquals($loadProcessorMock, $values[0], 'Load Processor is registered incorrectly.');
    }
}
