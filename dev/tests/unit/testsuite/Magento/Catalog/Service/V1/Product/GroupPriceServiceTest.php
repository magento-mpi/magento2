<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product;


class GroupPriceServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupPriceService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupServiceMock;

    protected function setUp()
    {
        $this->productFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory', array('create'), array(), '', false
        );
        $this->repositoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductRepository', array(), array(), '', false
        );
        $this->priceBuilderMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\GroupPriceBuilder', array(), array(), '', false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->groupServiceMock = $this->getMock('\Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $this->service = new GroupPriceService(
            $this->productFactoryMock,
            $this->repositoryMock,
            $this->priceBuilderMock,
            $this->storeManagerMock,
            $this->groupServiceMock
        );
    }

    public function testGetList()
    {
        $this->markTestIncomplete();
    }
} 
