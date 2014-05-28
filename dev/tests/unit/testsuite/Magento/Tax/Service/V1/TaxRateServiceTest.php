<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Model\Calculation\Rate as RateModel;
use Magento\Tax\Service\V1\Data\TaxRate;
use Magento\TestFramework\Helper\ObjectManager;

class TaxRateServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxRateServiceInterface
     */
    private $taxRateService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\RateFactory
     */
    private $rateModelFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\Rate
     */
    private $rateModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Service\V1\Data\TaxRate
     */
    private $taxRateDataObjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\Rate\Converter
     */
    private $converterMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->rateModelFactoryMock = $this->getMockBuilder(
            'Magento\Tax\Model\Calculation\RateFactory'
        )->disableOriginalConstructor()->setMethods(
                array('create')
            )->getMock();
        $this->rateModelMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->taxRateDataObjectMock = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxRate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->converterMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->taxRateService = $this->objectManager->getObject('Magento\Tax\Service\V1\TaxRateService',
            [
                'rateFactory' => $this->rateModelFactoryMock,
                'converter' => $this->converterMock,
            ]
        );
    }

    public function testCreateTaxRate()
    {
        $taxData = [
            'country_id' => 'US',
            'region_id' => '8',
            'percentage_rate' => '8.25',
            'code' => 'US-CA-*-Rate',
            'zip_range' => ['from' => 78765, 'to' => 78780]
        ];
        $zipRangeBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\ZipRangeBuilder');
        $taxRateBuilder = $this->objectManager->getObject(
            'Magento\Tax\Service\V1\Data\TaxRateBuilder',
            ['zipRangeBuilder' => $zipRangeBuilder]
        );
        $taxRateDataObject = $taxRateBuilder->populateWithArray($taxData)->create();
        $this->rateModelMock->expects($this->once())
            ->method('save')
            ->will($this->returnValue($this->rateModelMock));
        $this->converterMock->expects($this->once())
            ->method('createTaxRateModel')
            ->will($this->returnValue($this->rateModelMock));
        $this->converterMock->expects($this->once())
            ->method('createTaxRateDataObjectFromModel')
            ->will($this->returnValue($taxRateDataObject));
        $taxRateServiceData = $this->taxRateService->createTaxRate($taxRateDataObject);

        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\TaxRate', $taxRateServiceData);
        $this->assertEquals($taxData['country_id'], $taxRateServiceData->getCountryId());
        $this->assertEquals($taxData['region_id'], $taxRateServiceData->getRegionId());
        $this->assertEquals($taxData['percentage_rate'], $taxRateServiceData->getPercentageRate());
        $this->assertEquals($taxData['code'], $taxRateServiceData->getCode());
        $this->assertEquals($taxData['region_id'], $taxRateServiceData->getRegionId());
        $this->assertEquals($taxData['percentage_rate'], $taxRateServiceData->getPercentageRate());
        $this->assertEquals($taxData['zip_range']['from'], $taxRateServiceData->getZipRange()->getFrom());
        $this->assertEquals($taxData['zip_range']['to'], $taxRateServiceData->getZipRange()->getTo());

    }

    /**
     * @param array $returnValues
     * @param array $expectedResult
     * @dataProvider getTaxRatesDataProvider
     */
    public function testGetTaxRates($itemCounts, $expectedResult)
    {
        $rateModelMocks = [];
        $rateModelMocks[] = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->disableOriginalConstructor()
            ->getMock();
        $rateModelMocks[] = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->disableOriginalConstructor()
            ->getMock();
        $taxRateDataObjectMocks = [];
        $taxRateDataObjectMocks[] = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxRate')
            ->disableOriginalConstructor()
            ->getMock();
        $taxRateDataObjectMocks[] = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxRate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->converterMock->expects($this->any())
            ->method('createTaxRateDataObjectFromModel')
            ->will($this->returnValueMap(
                    [
                        [$rateModelMocks[0], $taxRateDataObjectMocks[0]],
                        [$rateModelMocks[1], $taxRateDataObjectMocks[1]]
                    ]
                )
            );
        $collectionMock = $this->getMockBuilder('\Magento\Tax\Model\Resource\Calculation\Rate\Collection')
            ->disableOriginalConstructor()
            ->setMethods(
                ['getSize', 'getItems', 'getIterator']
            )->getMock();
        $items = [];
        for ($i = 0; $i < $itemCounts; $i++) {
            $items[] = $rateModelMocks[$i];
        }
        $this->mockReturnValue(
            $collectionMock,
            [
                'getSize' => $itemCounts,
                'getItems' => $items,
                'getIterator' => new \ArrayIterator($items)
            ]
        );

        $this->rateModelFactoryMock->expects(
            $this->atLeastOnce()
        )->method(
                'create'
            )->will(
                $this->returnValue($this->rateModelMock)
            );

        $this->mockReturnValue(
            $this->rateModelMock,
            [
                'getResourceCollection' => $collectionMock,
            ]
        );

        $taxRates = $this->taxRateService->getTaxRates();
        $this->assertEquals($expectedResult, count($taxRates));
        for ($i = 0; $i < $itemCounts; $i++) {
            $this->assertEquals($taxRateDataObjectMocks[$i], $taxRates[$i]);
        }
    }

    public function getTaxRatesDataProvider()
    {
        return [
            [0, 0],
            [1, 1],
            [2, 2],
        ];
    }

    public function testUpdateTaxRate()
    {
        $taxRateBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRateBuilder');
        $taxRate = $taxRateBuilder
            ->setId(2)
            ->setCode('Rate-Code')
            ->setCountryId('US')
            ->setPercentageRate(0.1)
            ->setPostcode('55555')
            ->setRegionId('TX')
            ->create();
        $mockModel = $this->createMockModel($taxRate);
        $this->converterMock->expects($this->once())
            ->method('createTaxRateModel')
            ->with($taxRate)
            ->will($this->returnValue($mockModel));

        $result = $this->taxRateService->updateTaxRate($taxRate);

        $this->assertTrue($result);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testUpdateTaxRateNoId()
    {
        $taxRateBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRateBuilder');
        $taxRate = $taxRateBuilder
            ->setCode('Rate-Code')
            ->setCountryId('US')
            ->setPercentageRate(0.1)
            ->setPostcode('55555')
            ->setRegionId('TX')
            ->create();
        $this->converterMock->expects($this->once())
            ->method('createTaxRateModel')
            ->with($taxRate)
            ->will($this->throwException(new NoSuchEntityException()));

        $this->taxRateService->updateTaxRate($taxRate);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testUpdateTaxRateMissingRequiredInfo()
    {
        $taxRateBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRateBuilder');
        $taxRate = $taxRateBuilder
            ->setId(2)
            ->setCode('Rate-Code')
            ->setCountryId('US')
            ->setPercentageRate(0.1)
            ->setRegionId('TX')
            ->create();

        $this->taxRateService->updateTaxRate($taxRate);
    }

    /**
     * Creates a mock Rate model from a given TaxRate data object.
     *
     * @param TaxRate $taxRate
     * @return \PHPUnit_Framework_MockObject_MockObject|RateModel
     */
    private function createMockModel(TaxRate $taxRate)
    {
        $mockModel = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->setMethods(['getCode', 'getTaxCountryId', 'getTaxRegionId', 'getTaxPostcode', 'getRate',
                    'getZipFrom', 'getZipTo', 'getZipIsRange', '__wakeup'])
            ->disableOriginalConstructor()->getMock();
        $mockModel->expects($this->any())
            ->method('getCode')->will($this->returnValue($taxRate->getCode()));
        $mockModel->expects($this->any())
            ->method('getTaxCountryId')->will($this->returnValue($taxRate->getCountryId()));
        $mockModel->expects($this->any())
            ->method('getTaxRegionId')->will($this->returnValue($taxRate->getRegionId()));
        $mockModel->expects($this->any())
            ->method('getTaxPostcode')->will($this->returnValue($taxRate->getPostcode()));
        $mockModel->expects($this->any())
            ->method('getRate')->will($this->returnValue($taxRate->getPercentageRate()));
        $isZipRange = (bool)$taxRate->getZipRange();
        $mockModel->expects($this->any())
            ->method('getZipIsRange')->will($this->returnValue($isZipRange));
        if ($isZipRange) {
            $mockModel->expects($this->any())
                ->method('getZipFrom')->will($this->returnValue($taxRate->getZipRange()->getFrom()));
            $mockModel->expects($this->any())
                ->method('getZipTo')->will($this->returnValue($taxRate->getZipRange()->getTo()));
        }

        return $mockModel;
    }

    /**
     * Mock return value
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())->method($method)->will($this->returnValue($value));
        }
    }

}
