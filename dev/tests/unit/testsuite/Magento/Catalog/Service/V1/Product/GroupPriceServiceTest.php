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
    protected $productMock;

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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceModifierMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $websiteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    protected function setUp()
    {
        $this->productFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory', array('create'), array(), '', false
        );
        $this->repositoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductRepository', array(), array(), '', false
        );
        $this->priceBuilderMock = $this->getMock(
            'Magento\Catalog\Service\V1\Data\Product\GroupPriceBuilder', array(), array(), '', false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->groupServiceMock = $this->getMock('\Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $this->priceModifierMock =
            $this->getMock('Magento\Catalog\Model\Product\PriceModifier', array(), array(), '', false);
        $this->websiteMock =
            $this->getMock('Magento\Store\Model\Website', array('getId', '__wakeup'), array(), '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product',
            array('getData', 'getIdBySku', 'load', '__wakeup'), array(), '', false);
        $this->productFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->productMock));
        $this->configMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->service = new GroupPriceService(
            $this->productFactoryMock,
            $this->repositoryMock,
            $this->priceBuilderMock,
            $this->storeManagerMock,
            $this->groupServiceMock,
            $this->priceModifierMock,
            $this->configMock
        );
    }

    /**
     * @param string $configValue
     * @param array  $groupData
     * @param array $expected
     * @dataProvider getListDataProvider
     */
    public function testGetList($configValue, $groupData, $expected)
    {
        $this->repositoryMock->expects($this->once())->method('get')->with('product_sku')
            ->will($this->returnValue($this->productMock));
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue(array($groupData)));
        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
            ->will($this->returnValue($configValue));
        $this->priceBuilderMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($expected);
        $this->priceBuilderMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue('data'));
        $prices = $this->service->getList('product_sku');
        $this->assertCount(1, $prices);
        $this->assertEquals('data', $prices[0]);
    }

    public function getListDataProvider()
    {
        return array(
            array(
                1,
                array('website_price' => 10, 'price' => 5, 'all_groups' => 1),
                array('customer_group_id' => 'all', 'value' => 10)
            ),
            array(
                0,
                array('website_price' => 10, 'price' => 5, 'all_groups' => 0, 'cust_group' => 1),
                array('customer_group_id' => 1, 'value' => 5)
            )
        );
    }

    public function testSuccessDeleteGroupPrice()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getIdBySku')
            ->with('product_sku')
            ->will($this->returnValue(1));
        $this->productMock->expects($this->once())->method('load')->with(1)->will($this->returnSelf());
        $this->storeManagerMock
            ->expects($this->never())
            ->method('getWebsite');
        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
            ->will($this->returnValue(0));
        $this->priceModifierMock->expects($this->once())->method('removeGroupPrice')->with($this->productMock, 4, 0);

        $this->assertEquals(true, $this->service->delete('product_sku', 4));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @message Such product doesn't exist
     */
    public function testDeleteGroupPriceFromNonExistingProduct()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getIdBySku')
            ->with('product_sku')
            ->will($this->returnValue(null));
        $this->productMock->expects($this->never())->method('load');
        $this->priceModifierMock->expects($this->never())->method('removeGroupPrice');
        $this->storeManagerMock
            ->expects($this->never())
            ->method('getWebsite');
        $this->service->delete('product_sku', null, 10);
    }

    public function testSuccessDeleteGroupPriceFromWebsiteLevel()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getIdBySku')
            ->with('product_sku')
            ->will($this->returnValue(1));
        $this->productMock->expects($this->once())->method('load')->with(1)->will($this->returnSelf());
        $this->storeManagerMock
            ->expects($this->once())
            ->method('getWebsite')
            ->will($this->returnValue($this->websiteMock));
        $this->websiteMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
            ->will($this->returnValue(1));
        $this->priceModifierMock->expects($this->once())->method('removeGroupPrice')->with($this->productMock, 4, 1);

        $this->assertEquals(true, $this->service->delete('product_sku', 4));
    }
}
