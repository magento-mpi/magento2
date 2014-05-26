<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;


class ProductServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testCreate()
    {
        $initializationHelper = $this
            ->getMockBuilder('Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper')
            ->disableOriginalConstructor()
            ->getMock();

        $productMapper= $this
            ->getMockBuilder('Magento\Catalog\Service\V1\Data\ProductMapper')
            ->disableOriginalConstructor()
            ->getMock();

        $productTypeManager = $this
            ->getMockBuilder('Magento\Catalog\Model\Product\TypeTransitionManager')
            ->disableOriginalConstructor()
            ->getMock();

        $productFactory = $this
            ->getMockBuilder('Magento\Catalog\Model\ProductFactory')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Catalog\Service\V1\ProductService $productService */
        $productService = $this->_objectManager->getObject(
            'Magento\Catalog\Service\V1\ProductService',
            [
                'initializationHelper' => $initializationHelper,
                'productMapper' => $productMapper,
                'productTypeManager' => $productTypeManager,
                'productFactory' => $productFactory,
            ]
        );

        $productModel = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $product = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $productMapper->expects($this->once())->method('toModel')->with($product)
            ->will($this->returnValue($productModel));

        $initializationHelper->expects($this->once())->method('initialize')->with($productModel);
        $productTypeManager->expects($this->once())->method('processProduct')->with($productModel);

        $productModel->expects($this->once())->method('validate');
        $productModel->expects($this->once())->method('save');

        $productId = 42;
        $productModel->expects($this->any())->method('getId')->will($this->returnValue($productId));

        $this->assertEquals($productId, $productService->create($product));

    }
} 