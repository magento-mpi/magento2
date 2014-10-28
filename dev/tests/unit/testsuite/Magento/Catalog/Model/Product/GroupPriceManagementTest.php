<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

use Magento\Framework\Exception\NoSuchEntityException;

class GroupPriceManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupPriceManagement
     */
    protected $groupPriceManagement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    protected function setUp()
    {
        $this->productRepositoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductRepository',
            array(),
            array(),
            '',
            false
        );
        $this->priceBuilderMock = $this->getMock(
            'Magento\Catalog\Api\Data\ProductGroupPriceInterfaceDataBuilder',
            array('populateWithArray', 'create'),
            array(),
            '',
            false
        );
        $this->groupServiceMock = $this->getMock('\Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $this->priceModifierMock =
            $this->getMock('Magento\Catalog\Model\Product\PriceModifier', array(), array(), '', false);
        $this->websiteMock =
            $this->getMock('Magento\Store\Model\Website', array('getId', '__wakeup'), array(), '', false);
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getData', 'setData', 'validate', 'save', 'getIdBySku', 'load', '__wakeup'),
            array(),
            '',
            false
        );
        $this->websiteMock =
            $this->getMock('Magento\Store\Model\Website', array('getId', '__wakeup'), array(), '', false);
        $this->productRepositoryMock->expects($this->any())->method('get')->with('product_sku')
            ->will($this->returnValue($this->productMock));
        $this->configMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->storeManagerMock = $this->getMock('Magento\Framework\StoreManagerInterface');
        $this->groupPriceManagement = new GroupPriceManagement(
            $this->productRepositoryMock,
            $this->priceBuilderMock,
            $this->groupServiceMock,
            $this->priceModifierMock,
            $this->configMock,
            $this->storeManagerMock
        );
    }

    /**
     * @param string $configValue
     * @param array $groupData
     * @param array $expected
     * @dataProvider getListDataProvider
     */
    public function testGetList($configValue, $groupData, $expected)
    {
        $this->productRepositoryMock->expects($this->once())->method('get')->with('product_sku', true)
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
        $prices = $this->groupPriceManagement->getList('product_sku');
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
        $this->storeManagerMock
            ->expects($this->never())
        ->method('getWebsite');
        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
            ->will($this->returnValue(0));
        $this->priceModifierMock->expects($this->once())->method('removeGroupPrice')->with($this->productMock, 4, 0);

        $this->assertEquals(true, $this->groupPriceManagement->remove('product_sku', 4));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @message Such product doesn't exist
     */
    public function testDeleteGroupPriceFromNonExistingProduct()
    {
        $this->productRepositoryMock->expects($this->once())->method('get')
            ->will($this->throwException(new NoSuchEntityException()));
        $this->priceModifierMock->expects($this->never())->method('removeGroupPrice');
        $this->storeManagerMock
            ->expects($this->never())
            ->method('getWebsite');
        $this->groupPriceManagement->remove('product_sku', null, 10);
    }

    public function testSuccessDeleteGroupPriceFromWebsiteLevel()
    {
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

        $this->assertEquals(true, $this->groupPriceManagement->remove('product_sku', 4));
    }

    public function testSetNewPriceWithGlobalPriceScope()
    {
        $groupBuilder = $this->getMock(
            '\Magento\Customer\Service\V1\Data\CustomerGroupBuilder',
            array(),
            array(),
            '',
            false
        );
        $groupBuilder->expects($this->any())->method('getData')->will($this->returnValue(array('id' => 1)));
        $group = new \Magento\Customer\Service\V1\Data\CustomerGroup($groupBuilder);
        $this->groupServiceMock->expects($this->once())->method('getGroup')->will($this->returnValue($group));
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue(array(array('cust_group' => 2, 'website_id' => 0, 'price' => 50))));
        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
            ->will($this->returnValue(1));

        $this->productMock->expects($this->once())->method('setData')->with(
            'group_price',
            array(
                array('cust_group' => 2, 'website_id' => 0, 'price' => 50),
                array('cust_group' => 1, 'website_id' => 0, 'price' => 100)
            )
        );

        $this->storeManagerMock->expects($this->once())->method('getWebsite')
            ->will($this->returnValue($this->websiteMock));
        $this->websiteMock->expects($this->once())->method('getId')
            ->will($this->returnValue(0));

        $this->productRepositoryMock->expects($this->once())->method('save')->with($this->productMock);
        $this->groupPriceManagement->add('product_sku', 1, 100);
    }

    public function testSetUpdatedPriceWithGlobalPriceScope()
    {
        $priceBuilder = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\Product\GroupPriceBuilder',
            array(),
            array(),
            '',
            false
        );
        $priceBuilder->expects($this->any())->method('getData')->will(
            $this->returnValue(
                array(
                    'customer_group_id' => 2,
                    'value' => 100
                )
            )
        );
        $price = new \Magento\Catalog\Service\V1\Data\Product\GroupPrice($priceBuilder);
        $groupBuilder = $this->getMock(
            '\Magento\Customer\Service\V1\Data\CustomerGroupBuilder',
            array(),
            array(),
            '',
            false
        );
        $groupBuilder->expects($this->any())->method('getData')->will($this->returnValue(array('id' => 1)));
        $group = new \Magento\Customer\Service\V1\Data\CustomerGroup($groupBuilder);
        $this->groupServiceMock->expects($this->once())->method('getGroup')->will($this->returnValue($group));
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue(array(array('cust_group' => 2, 'website_id' => 0, 'price' => 50))));
        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE)
            ->will($this->returnValue(0));

        $this->productMock->expects($this->once())->method('setData')->with(
            'group_price',
            array(
                array('cust_group' => 2, 'website_id' => 0, 'price' => 100),
            )
        );
        $this->productRepositoryMock->expects($this->once())->method('save')->with($this->productMock);
        $this->groupPriceManagement->add('product_sku', 2, 100);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Values of following attributes are invalid: attr1, attr2
     */
    public function testSetThrowsExceptionIfDoesntValidate()
    {
        $groupBuilder = $this->getMock(
            '\Magento\Customer\Service\V1\Data\CustomerGroupBuilder',
            array(),
            array(),
            '',
            false
        );
        $groupBuilder->expects($this->any())->method('getData')->will($this->returnValue(array('id' => 1)));
        $group = new \Magento\Customer\Service\V1\Data\CustomerGroup($groupBuilder);
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue(array()));

        $this->groupServiceMock->expects($this->once())->method('getGroup')->will($this->returnValue($group));
        $this->productMock->expects($this->once())->method('validate')->will(
            $this->returnValue(
                array('attr1' => '', 'attr2' => '')
            )
        );
        $this->productRepositoryMock->expects($this->never())->method('save');
        $this->groupPriceManagement->add('product_sku', 2, 100);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSetThrowsExceptionIfCantSave()
    {
        $groupBuilder = $this->getMock(
            '\Magento\Customer\Service\V1\Data\CustomerGroupBuilder',
            array(),
            array(),
            '',
            false
        );
        $groupBuilder->expects($this->any())->method('getData')->will($this->returnValue(array('id' => 1)));
        $group = new \Magento\Customer\Service\V1\Data\CustomerGroup($groupBuilder);
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue(array()));

        $this->groupServiceMock->expects($this->once())->method('getGroup')->will($this->returnValue($group));
        $this->productRepositoryMock->expects($this->once())->method('save')->will($this->throwException(new \Exception()));
        $this->groupPriceManagement->add('product_sku', 2, 100);
    }
}
