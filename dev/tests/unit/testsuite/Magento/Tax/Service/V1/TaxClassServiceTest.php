<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Test for \Magento\Tax\Service\V1\TaxClassService
 */
class TaxClassServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\ClassModelFactory
     */
    private $taxClassModelFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Class
     */
    private $taxClassModelMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->taxClassModelFactoryMock = $this->getMockBuilder('Magento\Tax\Model\ClassModelFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->taxClassModelMock = $this->getMockBuilder('Magento\Tax\Model\ClassModel')
            ->disableOriginalConstructor()
            ->setMethods(['load', 'getId', 'delete', '__wakeup'])
            ->getMock();

        $this->taxClassModelFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->taxClassModelMock));
    }

    public function testDeleteModelDeleteThrowsException()
    {
        $taxClassId = 1;

        $service = $this->createService();

        $this->taxClassModelMock->expects($this->once())
            ->method('load')
            ->with($taxClassId)
            ->will($this->returnValue($this->taxClassModelMock));

        $this->taxClassModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($taxClassId));

        $this->taxClassModelMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception()));

        $this->assertFalse($service->deleteTaxClass($taxClassId));
    }

    public function testDeleteModelDeleteSuccess()
    {
        $taxClassId = 1;

        $service = $this->createService();

        $this->taxClassModelMock->expects($this->once())
            ->method('load')
            ->with($taxClassId)
            ->will($this->returnValue($this->taxClassModelMock));

        $this->taxClassModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($taxClassId));

        $this->taxClassModelMock->expects($this->once())
            ->method('delete');

        $this->assertTrue($service->deleteTaxClass($taxClassId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteNonExistentModel()
    {
        $taxClassId = 1;

        $service = $this->createService();

        $this->taxClassModelMock->expects($this->once())
            ->method('load')
            ->with($taxClassId)
            ->will($this->returnValue($this->taxClassModelMock));

        $this->taxClassModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $service->deleteTaxClass($taxClassId);
    }

    /**
     * @return TaxClassService
     */
    private function createService()
    {
        $taxClassCollectionFactory = $this->getMockBuilder('Magento\Tax\Model\Resource\TaxClass\CollectionFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $searchResultsBuilder = $this->objectManager
            ->getObject('\Magento\Tax\Service\V1\Data\SearchResultsBuilder');

        $converter = $this->objectManager->getObject('Magento\Tax\Model\Converter');

        $taxClassService = $this->objectManager->getObject('Magento\Tax\Service\V1\TaxClassService',
            [
                'taxClassCollectionFactory' => $taxClassCollectionFactory,
                'taxClassModelFactory' => $this->taxClassModelFactoryMock,
                'searchResultsBuilder' => $searchResultsBuilder,
                'converter' => $converter
            ]
        );

        return $taxClassService;
    }
}
