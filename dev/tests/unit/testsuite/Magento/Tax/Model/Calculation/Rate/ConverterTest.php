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

    /**
     * @param array $data
     * @dataProvider createTaxRateModelDataProvider
     */
    public function testCreateTaxRateModel($data)
    {
        $zipRangeBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\ZipRangeBuilder');
        $taxRateBuilder = $this->objectManager->getObject(
            'Magento\Tax\Service\V1\Data\TaxRateBuilder',
            ['zipRangeBuilder' => $zipRangeBuilder]
        );
        /** @var  $taxRateDataObject \Magento\Tax\Service\V1\Data\TaxRate */
        $taxRateDataObject = $taxRateBuilder->populateWithArray($data)->create();
        $zipIsRange = $taxRateDataObject->getZipRange();
        $isZipRange = !empty($zipIsRange);

        $rateModelMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->setMethods(
                [
                    'getCode',
                    'getTaxCountryId',
                    'getTaxRegionId',
                    'getTaxPostcode',
                    'getRate',
                    'getZipFrom',
                    'getZipTo',
                    'getZipIsRange',
                    '__wakeup'
                ]
            )
            ->disableOriginalConstructor()->getMock();
        $rateModelMock->expects($this->any())
            ->method('getCode')->will($this->returnValue($taxRateDataObject->getCode()));
        $rateModelMock->expects($this->any())
            ->method('getTaxCountryId')->will($this->returnValue($taxRateDataObject->getCountryId()));
        $rateModelMock->expects($this->any())
            ->method('getTaxRegionId')->will($this->returnValue($taxRateDataObject->getRegionId()));
        $rateModelMock->expects($this->any())
            ->method('getTaxPostcode')->will($this->returnValue($taxRateDataObject->getPostcode()));
        $rateModelMock->expects($this->any())
            ->method('getRate')->will($this->returnValue($taxRateDataObject->getPercentageRate()));
        if ($isZipRange) {
            $rateModelMock->expects($this->any())
                ->method('getZipFrom')->will($this->returnValue($taxRateDataObject->getZipRange()->getFrom()));
            $rateModelMock->expects($this->any())
                ->method('getZipTo')->will($this->returnValue($taxRateDataObject->getZipRange()->getTo()));
        }

        $rateModelFactoryMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\RateFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $rateModelFactoryMock->expects($this->any())->method('create')->will($this->returnValue($rateModelMock));

        /** @var  $converter \Magento\Tax\Model\Calculation\Rate\Converter */
        $converter = $this->objectManager->getObject(
            'Magento\Tax\Model\Calculation\Rate\Converter',
            [
                'taxRateDataObjectBuilder' => $taxRateBuilder,
                'taxRateModelFactory' => $rateModelFactoryMock,
                'zipRangeDataObjectBuilder' => $zipRangeBuilder
            ]
        );
        /** @var  $taxRateModel \Magento\Tax\Model\Calculation\Rate */
        $taxRateModel = $converter->createTaxRateModel($taxRateDataObject);

        //Assertion
        $this->assertEquals($taxRateDataObject->getId(), $taxRateModel->getId());
        $this->assertEquals($taxRateDataObject->getCountryId(), $taxRateModel->getTaxCountryId());
        $this->assertEquals($taxRateDataObject->getRegionId(), $taxRateModel->getTaxRegionId());
        $this->assertEquals($taxRateDataObject->getPostcode(), $taxRateModel->getTaxPostcode());
        $this->assertEquals($taxRateDataObject->getcode(), $taxRateModel->getCode());
        $this->assertEquals($taxRateDataObject->getPercentageRate(), $taxRateModel->getRate());
        if ($isZipRange) {
            if ($taxRateDataObject->getZipRange()->getFrom() && $taxRateModel->getZipTo()) {
                $this->assertEquals(
                    $taxRateDataObject->getZipRange()->getFrom(),
                    $taxRateModel->getZipFrom()
                );
                $this->assertEquals(
                    $taxRateDataObject->getZipRange()->getTo(),
                    $taxRateModel->getZipTo()
                );
            } elseif ($taxRateDataObject->getZipRange()->getFrom()) {
                $this->assertEquals(
                    $taxRateDataObject->getZipRange()->getFrom(),
                    $taxRateModel->getZipFrom()
                );
                $this->assertNull($taxRateModel->getZipTo());
            } else {
                $this->assertEquals(
                    $taxRateDataObject->getZipRange()->getTo(),
                    $taxRateModel->getZipTo()
                );
                $this->assertNull($taxRateModel->getZipFrom());
            }
        } else {
            $this->assertNull($taxRateModel->getZipFrom());
            $this->assertNull($taxRateModel->getZipTo());
        }
    }

    public function createTaxRateModelDataProvider()
    {
        return [
            'withZipRange' => [
                [
                    'id' => '1',
                    'countryId' => 'US',
                    'regionId' => '34',
                    'code' => 'US-CA-*-Rate 2',
                    'percentage_rate' => '8.25',
                    'zip_range' => ['from' => 78765, 'to' => 78780]
                ],
            ],
            'withZipRangeFrom' => [
                [
                    'id' => '1',
                    'countryId' => 'US',
                    'regionId' => '34',
                    'code' => 'US-CA-*-Rate 2',
                    'percentage_rate' => '8.25',
                    'zip_range' => ['from' => 78765]
                ],
            ],
            'withZipRangeTo' => [
                [
                    'id' => '1',
                    'countryId' => 'US',
                    'regionId' => '34',
                    'code' => 'US-CA-*-Rate 2',
                    'percentage_rate' => '8.25',
                    'zip_range' => ['to' => 78780]
                ],
            ],
            'withPostalCode' => [
                [
                    'id' => '1',
                    'countryId' => 'US',
                    'code' => 'US-CA-*-Rate 1',
                    'rate' => '8.25',
                    'postcode' => '78727'
                ],
            ]
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
