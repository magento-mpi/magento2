<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

/**
 * Test for \Magento\Catalog\Service\V1\ProductService
 */
class ProductServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Model\Product
     */
    protected $_productMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_productFactoryMock = $this->getMockBuilder('Magento\Catalog\Model\ProductFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->_productFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_productMock));
    }

    public function testDelete()
    {
        $productId = 100;
        $this->_productMock->expects($this->at(0))->method('load')->with($productId);
        $this->_productMock->expects($this->at(1))->method('getId')->will($this->returnValue(true));
        $this->_productMock->expects($this->at(2))->method('delete');

        $productService = $this->_createService();
        $this->assertTrue($productService->delete($productId));
    }

    public function testDeleteNoSuchEntityException()
    {
        $productId = 100;
        $this->_productMock->expects($this->at(0))->method('load')->with($productId);
        $this->_productMock->expects($this->at(1))->method('getId')->will($this->returnValue(false));

        $this->setExpectedException('Magento\Framework\Exception\NoSuchEntityException',
            "No such entity with id = $productId");
        $productService = $this->_createService();
        $this->assertTrue($productService->delete($productId));
    }

    /**
     * @return ProductService
     */
    private function _createService()
    {
        $productService = $this->_objectManager->getObject('Magento\Catalog\Service\V1\ProductService',
            [
                'productFactory' => $this->_productFactoryMock
            ]
        );
        return $productService;
    }
}
