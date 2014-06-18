<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\Tax\Service\V1\Data\TaxClass;
use Magento\Tax\Service\V1\Data\TaxClassBuilder;

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
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Tax\Model\Class
     */
    private $converterMock;

    /**
     * @var TaxClassBuilder
     */
    private $taxClassBuilder;

    /**
     * @var TaxClassService
     */
    private $taxClassService;

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
            ->setMethods(['load', 'getId', 'delete', 'save', '__wakeup'])
            ->getMock();

        $this->taxClassModelFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->taxClassModelMock));

        $this->taxClassBuilder = $this->objectManager->getObject('Magento\Tax\Service\V1\Data\TaxClassBuilder');

        $this->taxClassService = $this->createService();
    }

    public function testCreateTaxClass()
    {
        $taxClassId = 1;;

        $taxClassSample = $this->taxClassBuilder
            ->setType(TaxClass::TYPE_PRODUCT)
            ->setName('Wholesale product')
            ->create();

        $this->taxClassModelMock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($taxClassId));

        $this->taxClassModelMock
            ->expects($this->once())
            ->method('save');

        $this->converterMock
            ->expects($this->once())
            ->method('createTaxClassModel')
            ->with($taxClassSample)
            ->will($this->returnValue($this->taxClassModelMock));

        $this->assertEquals($taxClassId, $this->taxClassService->createTaxClass($taxClassSample));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage A class with the same name already exists for ClassType PRODUCT.
     */
    public function testCreateTaxClassException()
    {
        $taxClassSample = $this->taxClassBuilder
            ->setType(TaxClass::TYPE_PRODUCT)
            ->setName('Wholesale product')
            ->create();

        $this->taxClassModelMock
            ->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \Magento\Framework\Model\Exception()));

        $this->taxClassModelMock
            ->expects($this->never())
            ->method('getId');

        $this->converterMock
            ->expects($this->once())
            ->method('createTaxClassModel')
            ->with($taxClassSample)
            ->will($this->returnValue($this->taxClassModelMock));

        $this->taxClassService->createTaxClass($taxClassSample);
    }

    public function testCreateTaxClassInvalidData()
    {
        $taxClassSample = $this->taxClassBuilder
            ->create();

        $this->taxClassModelMock
            ->expects($this->never())
            ->method('save');

        $this->taxClassModelMock
            ->expects($this->never())
            ->method('getId');

        //Make sure that the conversion is avoided in case of data validation
        $this->converterMock
            ->expects($this->never())
            ->method('createTaxClassModel');

        try {
            $this->taxClassService->createTaxClass($taxClassSample);
        } catch(InputException $e) {
            $errors = $e->getErrors();
            $this->assertEquals('name is a required field.', $errors[0]->getMessage());
            $this->assertEquals('type is a required field.', $errors[1]->getMessage());
            $this->assertEquals('Invalid value of "" provided for the type field.', $errors[2]->getMessage());
        }
    }

    public function testDeleteModelDeleteThrowsException()
    {
        $taxClassId = 1;

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

        $this->assertFalse($this->taxClassService->deleteTaxClass($taxClassId));
    }

    public function testDeleteModelDeleteSuccess()
    {
        $taxClassId = 1;

        $this->taxClassModelMock->expects($this->once())
            ->method('load')
            ->with($taxClassId)
            ->will($this->returnValue($this->taxClassModelMock));

        $this->taxClassModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($taxClassId));

        $this->taxClassModelMock->expects($this->once())
            ->method('delete');

        $this->assertTrue($this->taxClassService->deleteTaxClass($taxClassId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteNonExistentModel()
    {
        $taxClassId = 1;

        $this->taxClassModelMock->expects($this->once())
            ->method('load')
            ->with($taxClassId)
            ->will($this->returnValue($this->taxClassModelMock));

        $this->taxClassModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $this->taxClassService->deleteTaxClass($taxClassId);
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

        $this->converterMock = $this->getMockBuilder('Magento\Tax\Model\Converter')
            ->disableOriginalConstructor()
            ->getMock();

        $taxClassService = $this->objectManager->getObject(
            'Magento\Tax\Service\V1\TaxClassService',
            [
                'taxClassCollectionFactory' => $taxClassCollectionFactory,
                'taxClassModelFactory' => $this->taxClassModelFactoryMock,
                'searchResultsBuilder' => $searchResultsBuilder,
                'converter' => $this->converterMock
            ]
        );

        return $taxClassService;
    }
}
