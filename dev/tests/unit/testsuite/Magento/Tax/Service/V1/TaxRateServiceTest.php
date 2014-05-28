<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

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

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->rateRegistryMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\RateRegistry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->converterMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->taxRateService = $this->createService();
    }

    public function testGetTaxRate()
    {
        $rateModelMock = $this->getMockBuilder('Magento\Tax\Model\Calculation\Rate')
            ->disableOriginalConstructor()
            ->getMock();
        $taxRateDataObjectMock = $this->getMockBuilder('Magento\Tax\Service\V1\Data\TaxRate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rateRegistryMock->expects($this->once())
            ->method('retrieveTaxRate')
            ->with(1)
            ->will($this->returnValue($rateModelMock));
        $this->converterMock->expects($this->once())
            ->method('createTaxRateDataObjectFromModel')
            ->with($rateModelMock)
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

    /**
     * Create service
     *
     * @return TaxRateService
     */
    private function createService()
    {
        return $this->objectManager->getObject('Magento\Tax\Service\V1\TaxRateService',
            [
                'rateRegistry' => $this->rateRegistryMock,
                'converter' => $this->converterMock,
            ]
        );
    }
}
