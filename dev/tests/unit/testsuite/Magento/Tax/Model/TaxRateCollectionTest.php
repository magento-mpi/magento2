<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model;

class TaxRateCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Model\TaxRateCollection
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxRateRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rateConverterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilderMock;

    protected function setUp()
    {
        $entityFactoryMock = $this->getMock('\Magento\Core\Model\EntityFactory', [], [], '', false);
        $filterBuilderMock = $this->getMock('\Magento\Framework\Api\FilterBuilder', [], [], '', false);
        $sortOrderBuilderMock = $this->getMock('\Magento\Framework\Api\SortOrderBuilder', [], [], '', false);
        $this->taxRateRepositoryMock = $this->getMock('\Magento\Tax\Api\TaxRateRepositoryInterface', [], [], '', false);
        $this->rateConverterMock = $this->getMock('\Magento\Tax\Model\Calculation\Rate\Converter', [], [], '', false);
        $this->searchCriteriaBuilderMock = $this->getMock(
            '\Magento\Framework\Api\SearchCriteriaBuilder',
            [],
            [],
            '',
            false
        );
        $this->model = new \Magento\Tax\Model\TaxRateCollection(
            $entityFactoryMock,
            $filterBuilderMock,
            $this->searchCriteriaBuilderMock,
            $sortOrderBuilderMock,
            $this->taxRateRepositoryMock,
            $this->rateConverterMock
        );
    }

    /**
     * @dataProvider createTaxRateCollectionItemDataProvider
     * @param $zipFrom int|null
     * @param $zipTo int|null
     */
    public function testLoadData($zipFrom, $zipTo)
    {
        $taxId = 42;
        $taxCode = 'taxCode';
        $taxCountryId = 'US';
        $taxRegionId = 'CA';
        $taxRegionName = 'California';
        $taxPostcode = '1235674';
        $taxRate = 8.375;
        $taxTitles = ['taxTitle'];
        $searchCriteriaMock = $this->getMock('\Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $searchResultsMock = $this->getMock('\Magento\Tax\Api\Data\TaxRateSearchResultsInterface', [], [], '', false);
        $taxRateMock = $this->getMock('\Magento\Tax\Api\Data\TaxRateInterface', [], [], '', false);

        $this->searchCriteriaBuilderMock->expects($this->once())->method('setCurrentPage')->with(1);
        $this->searchCriteriaBuilderMock->expects($this->once())->method('setPageSize')->with(false);
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteriaMock);
        $this->taxRateRepositoryMock->expects($this->once())->method('getList')->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())->method('getTotalCount')->willReturn(42);
        $searchResultsMock->expects($this->once())->method('getItems')->willReturn([$taxRateMock]);
        $taxRateMock->expects($this->once())->method('getId')->willReturn($taxId);
        $taxRateMock->expects($this->once())->method('getCode')->willReturn($taxCode);
        $taxRateMock->expects($this->once())->method('getTaxCountryId')->willReturn($taxCountryId);
        $taxRateMock->expects($this->once())->method('getTaxRegionId')->willReturn($taxRegionId);
        $taxRateMock->expects($this->once())->method('getRegionName')->willReturn($taxRegionName);
        $taxRateMock->expects($this->once())->method('getTaxPostcode')->willReturn($taxPostcode);
        $taxRateMock->expects($this->once())->method('getRate')->willReturn($taxRate);
        $this->rateConverterMock->expects($this->once())->method('createTitleArrayFromServiceObject')
            ->with($taxRateMock)->willReturn($taxTitles);
        $taxRateMock->expects($this->atLeastOnce())->method('getZipTo')->willReturn($zipTo);
        $taxRateMock->expects($this->any())->method('getZipFrom')->willReturn($zipFrom);
        $this->assertEquals($this->model, $this->model->loadData());
        $this->assertTrue($this->model->isLoaded());
    }

    public function createTaxRateCollectionItemDataProvider()
    {
        return [
            [null, null],
            [100, 200]
        ];
    }
}
