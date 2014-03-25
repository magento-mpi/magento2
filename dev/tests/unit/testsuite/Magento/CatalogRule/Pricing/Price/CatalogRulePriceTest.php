<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Pricing\Price;

/**
 * Class CatalogRulePriceTest
 */
class CatalogRulePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $catalogRulePrice;
    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataTimeMock;

    /**
     * @var \Magento\Core\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Pricing\PriceInfo
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\CatalogRule\Model\Resource\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleResourceFactoryMock;

    /**
     * @var \Magento\CatalogRule\Model\Resource\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleResourceMock;

    /**
     * @var \Magento\Core\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreWebsiteMock;

    /**
     * @var \Magento\Core\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreStoreMock;

    /**
     * @var float
     */
    protected $baseAmountMock = 100;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->initMocks();

        $this->priceInfoMock->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue([]));
        $this->salableItemMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $coreStoreId = 1;
        $coreWebsiteId = 1;
        $productId = 1;
        $customerGroupId = 1;
        $dateTime = time();

        $this->salableItemMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productId));

        $this->catalogRuleResourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->catalogRuleResourceMock));

        $this->storeManagerMock->expects($this->exactly(2))
            ->method('getCurrentStore')
            ->will($this->returnValue($this->coreStoreMock));
        $this->coreStoreMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($coreStoreId));
        $this->coreStoreMock->expects($this->once())
            ->method('getWebsiteId')
            ->will($this->returnValue($coreWebsiteId));
        $this->dataTimeMock->expects($this->once())
            ->method('scopeTimeStamp')
            ->with($this->equalTo($coreStoreId))
            ->will($this->returnValue($dateTime));
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerGroupId')
            ->will($this->returnValue($customerGroupId));
        $this->catalogRuleResourceMock->expects($this->once())
            ->method('getRulePrice')
            ->will($this->returnValue($this->baseAmountMock));

        $this->catalogRulePrice = new \Magento\CatalogRule\Pricing\Price\CatalogRulePrice(
            $this->salableItemMock,
            $this->dataTimeMock,
            $this->storeManagerMock,
            $this->customerSessionMock,
            $this->catalogRuleResourceFactoryMock
        );
    }

    /**
     * init mocks for setUp function
     */
    protected function initMocks()
    {
        $this->salableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getId', '__wakeup', 'getPriceInfo'],
            [],
            '',
            false
        );
        $this->dataTimeMock = $this->getMockForAbstractClass(
            'Magento\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->storeManagerMock = $this->getMock(
            'Magento\Core\Model\StoreManager',
            array(),
            array(),
            '',
            false
        );
        $this->customerSessionMock = $this->getMock(
            'Magento\Customer\Model\Session',
            [],
            [],
            '',
            false
        );
        $this->priceInfoMock = $this->getMock(
            '\Magento\Pricing\PriceInfo',
            ['getAdjustments'],
            [],
            '',
            false
        );
        $this->catalogRuleResourceFactoryMock = $this->getMock(
            '\Magento\CatalogRule\Model\Resource\RuleFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->catalogRuleResourceMock = $this->getMock(
            '\Magento\CatalogRule\Model\Resource\Rule',
            [],
            [],
            '',
            false
        );
        $this->coreStoreMock = $this->getMock(
            '\Magento\Core\Model\Store',
            [],
            [],
            '',
            false
        );
        $this->coreWebsiteMock = $this->getMock(
            '\Magento\Core\Model\Website',
            [],
            [],
            '',
            false
        );
    }

    /**
     * Test get Value
     */
    public function testGetValue()
    {
        $this->catalogRuleResourceFactoryMock->expects($this->never())
            ->method('create');
        $this->assertEquals($this->baseAmountMock, $this->catalogRulePrice->getValue());
    }
}
