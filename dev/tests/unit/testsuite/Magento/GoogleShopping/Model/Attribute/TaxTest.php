<?php
namespace Magento\GoogleShopping\Model\Attribute;

use Magento\Tax\Service\V1\Data\TaxRule;

class TaxTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Tax\Helper\Data | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockTaxHelper;

    /**
     * @var \Magento\GoogleShopping\Model\Resource\Attribute | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResource;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFilterBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSearchCriteriaBuilder;

    /**
     * @var \Magento\Tax\Service\V1\TaxRuleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockTaxRuleService;

    /**
     * @var \Magento\Tax\Service\V1\TaxRateServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockTaxRateService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockGroupService;

    /**
     * @var  \Magento\GoogleShopping\Model\Attribute\Tax
     */
    protected $model;

    public function setUp()
    {
        $this->mockTaxHelper = $this->getMockBuilder('\Magento\Tax\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockResource = $this->getMockBuilder('\Magento\GoogleShopping\Model\Resource\Attribute')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockFilterBuilder = $this->getMockBuilder('\Magento\Framework\Service\V1\Data\FilterBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockSearchCriteriaBuilder = $this->getMockBuilder(
            '\Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockTaxRuleService = $this->getMockBuilder('\Magento\Tax\Service\V1\TaxRuleServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockTaxRateService = $this->getMockBuilder('\Magento\Tax\Service\V1\TaxRateServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockGroupService = $this->getMockBuilder('\Magento\Customer\Service\V1\CustomerGroupServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = [
            'resource'              => $this->mockResource,
            'taxData'               => $this->mockTaxHelper,
            'filterBuilder'         => $this->mockFilterBuilder,
            'searchCriteriaBuilder' => $this->mockSearchCriteriaBuilder,
            'taxRuleService'        => $this->mockTaxRuleService,
            'taxRateService'        => $this->mockTaxRateService,
            'groupServiceInterface' => $this->mockGroupService
        ];
        $this->model = $objectManager->getObject('Magento\GoogleShopping\Model\Attribute\Tax', $arguments);
    }

    /**
     *
     */
    public function testConvertAttribute()
    {
        $this->markTestSkipped('TBD');
        $mockProduct = $this->getMockBuilder('\Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $mockEntry = $this->getMockBuilder('\Magento\Framework\Gdata\Gshopping\Entry')
            ->disableOriginalConstructor()
            ->getMock();

        $arguments = ['taxData' => $this->mockTaxHelper];

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\GoogleShopping\Model\Attribute\Tax | \PHPUnit_Framework_MockObject_MockObject $mockModel */
        $mockModel = $this->getMockBuilder('\Magento\GoogleShopping\Model\Attribute\Tax')
            ->setMethods(
                [
                    '__wakeup',
                    '_getDefaultCustomerTaxClass',
                    '_getRatesByCustomerAndProductTaxClasses',
                    '_getRegionsByRegionId',
                ]
            )
            ->setConstructorArgs(
                $objectManager->getConstructArguments('\Magento\GoogleShopping\Model\Attribute\Tax', $arguments)
            )
            ->getMock();

        $this->mockTaxHelper->expects($this->any())
            ->method('getConfig')
            ->will($this->returnSelf());
        $this->mockTaxHelper->expects($this->any())
            ->method('priceIncludesTax')
            ->will($this->returnValue(false));

        $mockModel->expects($this->once())
            ->method('_getDefaultCustomerTaxClass')
            ->will($this->returnValue(null));

        $this->assertSame($mockEntry, $mockModel->convertAttribute($mockProduct, $mockEntry));
    }

    public function testGetDefaultCustomerTaxClass()
    {
        $mockGroup = $this->getMockBuilder('\Magento\Customer\Service\V1\Data\CustomerGroup')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockGroupService->expects($this->once())
            ->method('getDefaultGroup')
            ->with('store')
            ->will($this->returnValue($mockGroup));

        $taxClassId = 'tax_class_id';

        $mockGroup->expects($this->once())
            ->method('getTaxClassId')
            ->will($this->returnValue($taxClassId));

        $reflectionObject = new \ReflectionObject($this->model);
        $reflectionMethod = $reflectionObject->getMethod('_getDefaultCustomerTaxClassId');
        $reflectionMethod->setAccessible(true);
        $this->assertSame($taxClassId, $reflectionMethod->invokeArgs($this->model, ['store']));
    }

    public function testGetRatesByCustomerAndProductTaxClasses()
    {
        $customerTaxClass = 0;
        $productTaxClass = 0;

        $filterOne = 'filter_one';
        $filterTwo = 'filter_two';

        $this->mockFilterBuilder->expects($this->exactly(2))
            ->method('setField')
            ->with(
                $this->logicalOr(
                    $this->equalTo(TaxRule::CUSTOMER_TAX_CLASS_IDS),
                    $this->equalTo(TaxRule::PRODUCT_TAX_CLASS_IDS)
                )
            )
            ->will($this->returnSelf());
        $this->mockFilterBuilder->expects($this->exactly(2))
            ->method('setValue')
            ->with(
                $this->logicalOr(
                    $this->equalTo([$customerTaxClass]),
                    $this->equalTo([$productTaxClass])
                )
            )
            ->will($this->returnSelf());
        $this->mockFilterBuilder->expects($this->at(2))
            ->method('create')
            ->will($this->returnValue($filterOne));
        $this->mockFilterBuilder->expects($this->at(5))
            ->method('create')
            ->will($this->returnValue($filterTwo));

        $this->mockSearchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->with([$filterOne, $filterTwo])
            ->will($this->returnSelf());

        $searchCriteria = $this->getMockBuilder('\Magento\Framework\Service\V1\Data\SearchCriteria')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockSearchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->will($this->returnValue($searchCriteria));

        $searchResults = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\TaxRuleSearchResults')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTaxRuleService->expects($this->once())
            ->method('searchTaxRules')
            ->with($searchCriteria)
            ->will($this->returnValue($searchResults));

        $mockTaxRule = $this->getMockBuilder('\Magento\Tax\Service\V1\Data\TaxRule')
            ->disableOriginalConstructor()
            ->getMock();
        $taxRules = [$mockTaxRule];

        $searchResults->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($taxRules));

        $mockTaxRule->expects($this->once())
            ->method('getTaxRateIds')
            ->will($this->returnValue([777]));

        $this->mockTaxRateService->expects($this->once())
            ->method('getTaxRate')
            ->with(777)
            ->will($this->returnValue(888));

        $reflectionObject = new \ReflectionObject($this->model);
        $reflectionMethod = $reflectionObject->getMethod('_getRatesByCustomerAndProductTaxClassID');
        $reflectionMethod->setAccessible(true);
        $this->assertSame([888], $reflectionMethod->invokeArgs($this->model, [$customerTaxClass, $productTaxClass]));
    }

    public function testGetRegionsByRegionId()
    {
        $regionId = 1;
        $postalCode = '*';
        $expectedResults = ['A', 'B', 'C'];

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject $mockAdapter */
        $mockAdapter = $this->getMockForAbstractClass('\Magento\Framework\DB\Adapter\AdapterInterface');
        $this->mockResource->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($mockAdapter));

        $mockSelect = $this->getMockBuilder('\Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();
        $mockAdapter->expects($this->once())
            ->method('select')
            ->will($this->returnValue($mockSelect));

        $someTableName = 'some_table_name';
        $this->mockResource->expects($this->once())
            ->method('getTable')
            ->with('directory_country_region')
            ->will($this->returnValue($someTableName));

        $mockSelect->expects($this->once())
            ->method('from')
            ->with(
                ['main_table' => $someTableName],
                ['state' => 'main_table.code']
            )
            ->will($this->returnSelf());

        $mockSelect->expects($this->once())
            ->method('where')
            ->with("main_table.region_id = $regionId");

        $dbResult = [['state' => $expectedResults]];
        $mockAdapter->expects($this->once())
            ->method('fetchAll')
            ->with($mockSelect)
            ->will($this->returnValue($dbResult));

        $reflectionObject = new \ReflectionObject($this->model);
        $reflectionMethod = $reflectionObject->getMethod('_getRegionsByRegionId');
        $reflectionMethod->setAccessible(true);
        $this->assertSame([$expectedResults], $reflectionMethod->invokeArgs($this->model, [$regionId, $postalCode]));
    }
}
