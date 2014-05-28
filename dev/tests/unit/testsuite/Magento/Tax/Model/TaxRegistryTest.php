<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Test for TaxRegistry
 *
 */
class TaxRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Model\TaxRegistry
     */
    private $taxRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\RateFactory
     */
    private $rateModelFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Calculation\Rate
     */
    private $rateModelMock;

    const TAX_RATE_ID = 1;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->rateModelFactoryMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\RateFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->taxRegistry = $objectManager->getObject(
            'Magento\Tax\Model\TaxRegistry',
            ['taxModelRateFactory' => $this->rateModelFactoryMock]
        );
        $this->rateModelMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testRegisterTaxRate()
    {
        $this->rateModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAX_RATE_ID));
        $this->taxRegistry->registerTaxRate($this->rateModelMock);
        $this->assertEquals($this->rateModelMock, $this->taxRegistry->retrieveTaxRate(self::TAX_RATE_ID));
    }

    public function testRetrieveTaxRate()
    {
        $this->rateModelMock->expects($this->once())
            ->method('load')
            ->with(self::TAX_RATE_ID)
            ->will($this->returnValue($this->rateModelMock));
        $this->rateModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::TAX_RATE_ID));
        $this->rateModelFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->rateModelMock));
        $actual = $this->taxRegistry->retrieveTaxRate(self::TAX_RATE_ID);
        $this->assertEquals($this->rateModelMock, $actual);
        $actualCached = $this->taxRegistry->retrieveTaxRate(self::TAX_RATE_ID);
        $this->assertEquals($this->rateModelMock, $actualCached);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $this->rateModelMock->expects($this->once())
            ->method('load')
            ->with(self::TAX_RATE_ID)
            ->will($this->returnValue($this->rateModelMock));
        $this->rateModelMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->rateModelFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->rateModelMock));
        $this->taxRegistry->retrieveTaxRate(self::TAX_RATE_ID);
    }
}
