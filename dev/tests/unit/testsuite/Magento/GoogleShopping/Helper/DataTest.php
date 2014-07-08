<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Helper;

use Magento\Tax\Service\V1\Data\TaxRule;

class DataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\GoogleShopping\Helper\Data
     */
    protected $data;

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

    public function setUp()
    {
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

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = [
            'filterBuilder'         => $this->mockFilterBuilder,
            'searchCriteriaBuilder' => $this->mockSearchCriteriaBuilder,
            'taxRuleService'        => $this->mockTaxRuleService,
            'taxRateService'        => $this->mockTaxRateService,
        ];
        $this->data = $objectManager->getObject('\Magento\GoogleShopping\Helper\Data', $arguments);
    }

    public function testGetRRatesByCustomerAndProductTaxClassId()
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

        $this->assertSame(
            [888],
            $this->data->getRatesByCustomerAndProductTaxClassId($customerTaxClass, $productTaxClass)
        );
    }
}
