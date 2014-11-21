<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Tax\Api\Data\TaxClassDataBuilder;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Tax\Api\TaxClassManagementInterface;

class TaxClassServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxClassService
     */
    private $taxClassService;

    /**
     * @var TaxClassDataBuilder
     */
    private $taxClassBuilder;

    /**
     * @var TaxClassModel
     */
    private $taxClassModel;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $predefinedTaxClasses;

    const SAMPLE_TAX_CLASS_NAME = 'Wholesale Customer';

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->taxClassService = $this->objectManager->create('Magento\Tax\Api\TaxClassRepositoryInterface');
        $this->taxClassBuilder = $this->objectManager->create('Magento\Tax\Api\Data\TaxClassDataBuilder');
        $this->taxClassModel = $this->objectManager->create('Magento\Tax\Model\ClassModel');
        $this->predefinedTaxClasses = [
            TaxClassManagementInterface::TYPE_PRODUCT => 'Taxable Goods',
            TaxClassManagementInterface::TYPE_CUSTOMER => 'Retail Customer'
        ];
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage class_id is not expected for this request.
     */
    public function testCreateTaxClass()
    {
        $taxClassDataObject = $this->taxClassBuilder
            ->setClassName(self::SAMPLE_TAX_CLASS_NAME)
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        $this->assertEquals(self::SAMPLE_TAX_CLASS_NAME, $this->taxClassModel->load($taxClassId)->getClassName());

        //Create another one with created id. Make sure its not updating the existing Tax class
        $taxClassDataObject = $this->taxClassBuilder
            ->setClassId($taxClassId)
            ->setClassName(self::SAMPLE_TAX_CLASS_NAME . uniqid())
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER)
            ->create();
        //Should not be allowed to set the classId. Will throw InputException
        $this->taxClassService->createTaxClass($taxClassDataObject);
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage A class with the same name already exists for ClassType PRODUCT.
     */
    public function testCreateTaxClassUnique()
    {
        //ClassType and name combination has to be unique.
        //Testing against existing Tax classes which are already setup when the instance is installed
        $taxClassDataObject = $this->taxClassBuilder
            ->setClassName($this->predefinedTaxClasses[TaxClassModel::TAX_CLASS_TYPE_PRODUCT])
            ->setClassType(TaxClassManagementInterface::TYPE_PRODUCT)
            ->create();
        $this->taxClassService->createTaxClass($taxClassDataObject);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateTaxClassInvalidData()
    {
        $taxClassDataObject = $this->taxClassBuilder->setClassName(null)
            ->setClassType('')
            ->create();
        try {
            $this->taxClassService->createTaxClass($taxClassDataObject);
        } catch (InputException $e) {
            $errors = $e->getErrors();
            $this->assertEquals('class_name is a required field.', $errors[0]->getMessage());
            $this->assertEquals('class_type is a required field.', $errors[1]->getMessage());
        }
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetTaxClass()
    {
        $taxClassName = 'Get Me';
        $taxClassDataObject = $this->taxClassBuilder
            ->setClassName($taxClassName)
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        $data = $this->taxClassService->getTaxClass($taxClassId);
        $this->assertEquals($taxClassId, $data->getClassId());
        $this->assertEquals($taxClassName, $data->getClassName());
        $this->assertEquals(TaxClassManagementInterface::TYPE_CUSTOMER, $data->getClassType());
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with class_id = -9999
     */
    public function testGetTaxClassWithNoSuchEntityException()
    {
        $this->taxClassService->getTaxClass(-9999);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testDeleteTaxClass()
    {
        $taxClassName = 'Delete Me';
        $taxClassDataObject = $this->taxClassBuilder->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);

        $this->assertTrue($this->taxClassService->deleteTaxClass($taxClassId));

        // Verify if the tax class is deleted
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            "No such entity with class_id = $taxClassId"
        );
        $this->taxClassService->deleteTaxClass($taxClassId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with class_id = 99999
     */
    public function testDeleteTaxClassInvalidData()
    {
        $nonexistentTaxClassId = 99999;
        $this->taxClassService->deleteTaxClass($nonexistentTaxClassId);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testUpdateTaxClassSuccess()
    {
        $taxClassName = 'New Class Name';
        $taxClassDataObject = $this->taxClassBuilder->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        $this->assertEquals($taxClassName, $this->taxClassModel->load($taxClassId)->getClassName());

        $updatedTaxClassName = 'Updated Class Name';
        $taxClassDataObject = $this->taxClassBuilder->setClassName($updatedTaxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->create();

        $this->assertTrue($this->taxClassService->updateTaxClass($taxClassId, $taxClassDataObject));

        $this->assertEquals($updatedTaxClassName, $this->taxClassModel->load($taxClassId)->getClassName());
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Invalid value of "" provided for the taxClassId field.
     */
    public function testUpdateTaxClassWithoutClassId()
    {
        $taxClassName = 'New Class Name';
        $taxClassDataObject = $this->taxClassBuilder->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->create();
        $this->taxClassService->updateTaxClass("", $taxClassDataObject);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with class_id = 99999
     */
    public function testUpdateTaxClassWithInvalidClassId()
    {
        $taxClassName = 'New Class Name';
        $nonexistentTaxClassId = 99999;
        $taxClassDataObject = $this->taxClassBuilder->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->create();
        $this->taxClassService->updateTaxClass($nonexistentTaxClassId, $taxClassDataObject);
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Updating classType is not allowed.
     */
    public function testUpdateTaxClassWithChangingClassType()
    {
        $taxClassName = 'New Class Name';
        $taxClassDataObject = $this->taxClassBuilder->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        $this->assertEquals($taxClassName, $this->taxClassModel->load($taxClassId)->getClassName());

        $updatedTaxClassName = 'Updated Class Name';
        $taxClassDataObject = $this->taxClassBuilder->setClassName($updatedTaxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_PRODUCT)
            ->create();

        $this->taxClassService->updateTaxClass($taxClassId, $taxClassDataObject);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetTaxClassId()
    {
        $taxClassName = 'Get Me';
        $taxClassDataObject = $this->taxClassBuilder
            ->setClassName($taxClassName)
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);
        /** @var \Magento\Tax\Api\Data\TaxClassKeyDataBuilder $taxClassKeyBuilder */
        $taxClassKeyBuilder = $this->objectManager->create('Magento\Tax\Api\Data\TaxClassKeyDataBuilder');
        $taxClassKeyTypeId = $taxClassKeyBuilder->populateWithArray(
            [
                TaxClassKeyInterface::KEY_TYPE => TaxClassKeyInterface::TYPE_ID,
                TaxClassKeyInterface::KEY_VALUE => $taxClassId,
            ]
        )->create();
        $this->assertEquals(
            $taxClassId,
            $this->taxClassService->getTaxClassId($taxClassKeyTypeId, TaxClassManagementInterface::TYPE_CUSTOMER)
        );
        $taxClassKeyTypeName = $taxClassKeyBuilder->populateWithArray(
            [
                TaxClassKeyInterface::KEY_TYPE => TaxClassKeyInterface::TYPE_NAME,
                TaxClassKeyInterface::KEY_VALUE => $taxClassName,
            ]
        )->create();
        $this->assertEquals(
            $taxClassId,
            $this->taxClassService->getTaxClassId($taxClassKeyTypeId, TaxClassManagementInterface::TYPE_CUSTOMER)
        );
        $this->assertNull($this->taxClassService->getTaxClassId(null));
        $this->assertEquals(
            null,
            $this->taxClassService->getTaxClassId($taxClassKeyTypeName, TaxClassManagementInterface::TYPE_PRODUCT)
        );
    }
}
