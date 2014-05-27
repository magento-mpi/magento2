<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation\Rate;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $taxRateFactory;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->taxRateFactory = $this->objectManager->create('Magento\Tax\Model\Calculation\RateFactory');
    }

    /**
     * @param array $data
     * @dataProvider createTaxRateDataObjectFromModelDataProvider
     */
    public function testCreateTaxRateDataObjectFromModel($data)
    {
        $taxRateModel = $this->taxRateFactory->create(['data' => $data]);

        $taxRateDataOjectBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxRateBuilder');
        $zipRangeDataObjectBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\ZipRangeBuilder');
        /** @var  $converter \Magento\Tax\Model\Calculation\Rate\Converter */
        $converter = $this->objectManager->create(
            'Magento\Tax\Model\Calculation\Rate\Converter',
            [
                'taxRateDataObjectBuilder' => $taxRateDataOjectBuilder,
                'zipRangeDataObjectBuilder' => $zipRangeDataObjectBuilder,
            ]
        );
        $taxRateDataObject = $converter->createTaxRateDataObjectFromModel($taxRateModel);
        $this->assertEquals($taxRateModel->getId(), $taxRateDataObject->getId());
        $this->assertEquals($taxRateModel->getTaxCountryId(), $taxRateDataObject->getCountryId());
        $this->assertEquals($taxRateModel->getTaxRegionId(), $taxRateDataObject->getRegionId());
        $this->assertEquals($taxRateModel->getTaxPostcode(), $taxRateDataObject->getPostcode());
        $this->assertEquals($taxRateModel->getCode(), $taxRateDataObject->getcode());
        $this->assertEquals($taxRateModel->getRate(), $taxRateDataObject->getPercentageRate());
        $zipIsRange = $taxRateModel->getZipIsRange();
        if ($zipIsRange) {
            $this->assertEquals(
                $taxRateModel->getZipFrom(),
                $taxRateDataObject->getZipRange()->getFrom()
            );
            $this->assertEquals(
                $taxRateModel->getZipTo(),
                $taxRateDataObject->getZipRange()->getTo()
            );
        } else {
            $this->assertNull($taxRateDataObject->getZipRange());
        }
    }

    public function createTaxRateDataObjectFromModelDataProvider()
    {
        return [
            [
                [
                    'id' => '1',
                    'countryId' => 'US',
                    'regionId' => '34',
                    'code' => 'US-CA-*-Rate 1',
                    'rate' => '8.25',
                    'zipIsRange' => '1',
                    'zipFrom' => '78701',
                    'zipTo' => '78759',
                ],
            ],
            [
                [
                    'id' => '1',
                    'countryId' => 'US',
                    'code' => 'US-CA-*-Rate 1',
                    'rate' => '8.25',
                ],
            ],
        ];
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function mockReturnValue(\PHPUnit_Framework_MockObject_MockObject $mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())->method($method)->will($this->returnValue($value));
        }
    }
}
