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
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @param array $valueMap
     * @dataProvider createTaxRateDataObjectFromModelDataProvider
     */
    public function testCreateTaxRateDataObjectFromModel($valueMap)
    {
        $taxRateModelMock = $this->getMockBuilder(
            'Magento\Tax\Model\Calculation\Rate'
        )->disableOriginalConstructor()->setMethods(
            [
                'getId',
                'getCountryId',
                'getRegionId',
                'getTaxPostcode',
                'getCode',
                'getRate',
                'getZipIsRange',
                'getZipFrom',
                'getZipTo',
                '__wakeup',
            ]
        )->getMock();
        $this->mockReturnValue($taxRateModelMock, $valueMap);

        $taxRateDataOjectBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\TaxRateBuilder');
        $zipRangeDataObjectBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\ZipRangeBuilder');
        /** @var  $converter \Magento\Tax\Model\Calculation\Rate\Converter */
        $converter = $this->objectManager->getObject(
            'Magento\Tax\Model\Calculation\Rate\Converter',
            [
                'taxRateDataObjectBuilder' => $taxRateDataOjectBuilder,
                'zipRangeDataObjectBuilder' => $zipRangeDataObjectBuilder,
            ]
        );
        $taxRateDataObject = $converter->createTaxRateDataObjectFromModel($taxRateModelMock);
        $this->assertEquals($this->getExpectedValue($valueMap, 'getId'), $taxRateDataObject->getId());
        $this->assertEquals($this->getExpectedValue($valueMap, 'getTaxCountryId'), $taxRateDataObject->getCountryId());
        $this->assertEquals($this->getExpectedValue($valueMap, 'getTaxRegionId'), $taxRateDataObject->getRegionId());
        $this->assertEquals($this->getExpectedValue($valueMap, 'getTaxPostcode'), $taxRateDataObject->getPostcode());
        $this->assertEquals($this->getExpectedValue($valueMap, 'getCode'), $taxRateDataObject->getcode());
        $this->assertEquals($this->getExpectedValue($valueMap, 'getRate'), $taxRateDataObject->getPercentageRate());
        $zipIsRange = $this->getExpectedValue($valueMap, 'getZipIsRange');
        if ($zipIsRange) {
            $this->assertEquals(
                $this->getExpectedValue($valueMap, 'getZipFrom'),
                $taxRateDataObject->getZipRange()->getFrom()
            );
            $this->assertEquals(
                $this->getExpectedValue($valueMap, 'getZipTo'),
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
                    'getId' => '1',
                    'getCountryId' => 'US',
                    'getRegionId' => '34',
                    'getCode' => 'US-CA-*-Rate 1',
                    'getRate' => '8.25',
                    'getZipIsRange' => '1',
                    'getZipFrom' => '78701',
                    'getZipTo' => '78759',
                ],
            ],
            [
                [
                    'getId' => '1',
                    'getCountryId' => 'US',
                    'getCode' => 'US-CA-*-Rate 1',
                    'getRate' => '8.25',
                ],
            ],
        ];
    }

    private function getExpectedValue($valueMap, $key)
    {
        return array_key_exists($key, $valueMap) ? $valueMap[$key] : null;
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
