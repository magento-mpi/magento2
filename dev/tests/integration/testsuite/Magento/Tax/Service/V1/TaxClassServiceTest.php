<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\Tax\Service\V1\Data\TaxClassBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Tax\Service\V1\Data\TaxClass as TaxClassDataObject;

class TaxClassServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxClassService
     */
    private $taxClassService;

    /**
     * @var TaxClassBuilder
     */
    private $taxClassBuilder;

    /**
     * @var TaxClassModel
     */
    private $taxClassModel;

    /**
     * @var \Magento\Framework\ObjectManager
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
        $this->taxClassService = $this->objectManager->create('Magento\Tax\Service\V1\TaxClassService');
        $this->taxClassBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\TaxClassBuilder');
        $this->taxClassModel = $this->objectManager->create('Magento\Tax\Model\ClassModel');
        $this->predefinedTaxClasses = [
            TaxClassDataObject::TYPE_PRODUCT => 'Taxable Goods',
            TaxClassDataObject::TYPE_CUSTOMER => 'Retail Customer'
        ];
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCreateTaxClass()
    {
        $taxClassDataObject = $this->taxClassBuilder->setClassName(self::SAMPLE_TAX_CLASS_NAME)
            ->setClassType(TaxClassDataObject::TYPE_CUSTOMER)
            ->create();
        $taxClassId = $this->taxClassService->createTaxClass($taxClassDataObject);

        $this->assertEquals(self::SAMPLE_TAX_CLASS_NAME, $this->taxClassModel->load($taxClassId)->getClassName());
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
            ->setClassType(TaxClassDataObject::TYPE_PRODUCT)
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
        } catch(InputException $e) {
            $errors = $e->getErrors();
            $this->assertEquals('class_name is a required field.', $errors[0]->getMessage());
            $this->assertEquals('class_type is a required field.', $errors[1]->getMessage());
            $this->assertEquals('Invalid value of "" provided for the class_type field.', $errors[2]->getMessage());
        }
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
            "No such entity with taxClassId = $taxClassId"
        );
        $this->taxClassService->deleteTaxClass($taxClassId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with taxClassId = 99999
     */
    public function testDeleteTaxClassInvalidData()
    {
        $nonexistentTaxClassId = 99999;
        $this->taxClassService->deleteTaxClass($nonexistentTaxClassId);
    }
}
