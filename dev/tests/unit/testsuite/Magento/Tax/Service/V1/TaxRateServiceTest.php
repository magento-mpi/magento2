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
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\RateRegistry
     */
    private $rateRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\Rate\Converter
     */
    private $converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\Rate
     */
    private $rateModelMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->rateRegistryMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\RateRegistry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->converterMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rateModelMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->taxRateService = $this->objectManager->getObject('Magento\Tax\Service\V1\TaxRateService',
            [
                'rateRegistry' => $this->rateRegistryMock,
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
            'postcode' => '78765-78780',
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
        $this->assertEquals($taxData['postcode'], $taxRateServiceData->getPostcode());

    }

    public function testGetTaxRate()
    {
        $taxRateDataObjectMock = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxRate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rateRegistryMock->expects($this->once())
            ->method('retrieveTaxRate')
            ->with(1)
            ->will($this->returnValue($this->rateModelMock));
        $this->converterMock->expects($this->once())
            ->method('createTaxRateDataObjectFromModel')
            ->with($this->rateModelMock)
            ->will($this->returnValue($taxRateDataObjectMock));
        $this->assertEquals($taxRateDataObjectMock, $this->taxRateService->getTaxRate(1));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with taxRateId = 1
     */
    public function testGetTaxRateWithNoSuchEntityException()
    {
        $rateId = 1;
        $this->rateRegistryMock->expects($this->once())
            ->method('retrieveTaxRate')
            ->with($rateId)
            ->will($this->throwException(NoSuchEntityException::singleField('taxRateId', $rateId)));
        $this->converterMock->expects($this->never())
            ->method('createTaxRateDataObjectFromModel');
        $this->taxRateService->getTaxRate($rateId);
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

    public function testDeleteTaxRate()
    {
        $this->rateRegistryMock->expects($this->once())
            ->method('retrieveTaxRate')
            ->with(1)
            ->will($this->returnValue($this->rateModelMock));
        $this->rateRegistryMock->expects($this->once())
            ->method('removeTaxRate')
            ->with(1)
            ->will($this->returnValue($this->rateModelMock));
        $this->taxRateService->deleteTaxRate(1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteTaxRateRetrieveException()
    {
        $this->rateRegistryMock->expects($this->once())
            ->method('retrieveTaxRate')
            ->with(1)
            ->will($this->throwException(new NoSuchEntityException()));
        $this->taxRateService->deleteTaxRate(1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteTaxRateRemoveException()
    {
        $this->rateRegistryMock->expects($this->once())
            ->method('retrieveTaxRate')
            ->with(1)
            ->will($this->returnValue($this->rateModelMock));
        $this->rateRegistryMock->expects($this->once())
            ->method('removeTaxRate')
            ->with(1)
            ->will($this->throwException(new NoSuchEntityException()));
        $this->taxRateService->deleteTaxRate(1);
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
}
