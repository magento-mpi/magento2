<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1;

use Magento\Tax\Model\Calculation\Rate as RateModel;
use Magento\Tax\Service\V1\Data\TaxRateBuilder;
use Magento\Tax\Service\V1\Data\TaxRate;
use Magento\TestFramework\Helper\ObjectManager;

class TaxRateServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxRateServiceInterface
     */
    private $taxRateService;

    /**
     * @var TaxRateBuilder
     */
    private $taxRateBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\Rate\Converter
     */
    private $converter;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->converter = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate\Converter')
            ->disableOriginalConstructor()->getMock();
        $this->taxRateService = $objectManager->getObject('Magento\Tax\Service\V1\TaxRateService',
            ['converter' => $this->converter]
        );
        $this->taxRateBuilder = $objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRateBuilder');
    }

    public function testUpdateTaxRate()
    {
        $taxRate = $this->taxRateBuilder
            ->setId(42)
            ->setCode('Rate-Code')
            ->setCountryId('US')
            ->setPercentageRate(0.1)
            ->setPostcode('55555')
            ->setRegionId('TX')
            ->create();
        $mockModel = $this->createMockModel($taxRate);
        $this->converter->expects($this->once())
            ->method('createTaxRateModel')
            ->with($taxRate)
            ->will($this->returnValue($mockModel));

        $result = $this->taxRateService->updateTaxRate($taxRate);

        $this->assertTrue($result);
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
