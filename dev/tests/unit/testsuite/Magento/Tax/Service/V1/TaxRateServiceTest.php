<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

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
    private $coverterMock;

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
        $this->taxRateService = $this->createService();
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
        for($i = 0; $i < $itemCounts; $i++) {
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
        for($i = 0; $i < $itemCounts; $i++) {
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

    /**
     * Create service
     *
     * @return TaxRateService
     */
    private function createService()
    {
        return $this->objectManager->getObject('Magento\Tax\Service\V1\TaxRateService',
            [
                'rateFactory' => $this->rateModelFactoryMock,
                'converter' => $this->converterMock,
            ]
        );
    }
}
