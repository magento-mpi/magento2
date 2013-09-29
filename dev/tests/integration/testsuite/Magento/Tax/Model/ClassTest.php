<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_ClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
    }

    public function testCheckClassCanBeDeletedCustomerClassAssertException()
    {
        /** @var $model Magento_Tax_Model_Class */
        $model = $this->_objectManager->create('Magento_Tax_Model_Class')->getCollection()
            ->setClassTypeFilter(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
            ->getFirstItem();

        $this->setExpectedException('Magento_Core_Exception');
        $model->checkClassCanBeDeleted();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCheckClassCanBeDeletedProductClassAssertException()
    {
        /** @var $model Magento_Tax_Model_Class */
        $model = $this->_objectManager->create('Magento_Tax_Model_Class')->getCollection()
            ->setClassTypeFilter(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
            ->getFirstItem();

        $this->_objectManager->create('Magento_Catalog_Model_Product')
            ->setTypeId('simple')->setAttributeSetId(4)
            ->setName('Simple Product')->setSku(uniqid())->setPrice(10)
            ->setMetaTitle('meta title')->setMetaKeyword('meta keyword')->setMetaDescription('meta description')
            ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->setTaxClassId($model->getId())
            ->save();

        $this->setExpectedException('Magento_Core_Exception');
        $model->checkClassCanBeDeleted();
    }

    /**
     * @dataProvider classesDataProvider
     */
    public function testCheckClassCanBeDeletedPositiveResult($classType)
    {
        /** @var $model Magento_Tax_Model_Class */
        $model = $this->_objectManager->create('Magento_Tax_Model_Class');
        $model->setClassName('TaxClass' . uniqid())
            ->setClassType($classType)
            ->isObjectNew(true);
        $model->save();

        $this->assertTrue($model->checkClassCanBeDeleted());
    }

    public function classesDataProvider()
    {
        return array(
            array(Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER),
            array(Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCheckClassCanBeDeletedCustomerClassUsedInTaxRule()
    {
        /** @var $registry Magento_Core_Model_Registry */
        $registry = $this->_objectManager->get('Magento_Core_Model_Registry');
        /** @var $taxRule Magento_Tax_Model_Calculation_Rule */
        $taxRule = $registry->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $customerClasses = $taxRule->getCustomerTaxClasses();

        /** @var $model Magento_Tax_Model_Class */
        $model = $this->_objectManager->create('Magento_Tax_Model_Class')
            ->load($customerClasses[0]);
        $this->setExpectedException('Magento_Core_Exception', 'You cannot delete this tax class because it is used in' .
            ' Tax Rules. You have to delete the rules it is used in first.');
        $model->checkClassCanBeDeleted();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCheckClassCanBeDeletedProductClassUsedInTaxRule()
    {
        /** @var $registry Magento_Core_Model_Registry */
        $registry = $this->_objectManager->get('Magento_Core_Model_Registry');
        /** @var $taxRule Magento_Tax_Model_Calculation_Rule */
        $taxRule = $registry->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $productClasses = $taxRule->getProductTaxClasses();

        /** @var $model Magento_Tax_Model_Class */
        $model = $this->_objectManager->create('Magento_Tax_Model_Class')
            ->load($productClasses[0]);
        $this->setExpectedException('Magento_Core_Exception', 'You cannot delete this tax class because it is used in' .
            ' Tax Rules. You have to delete the rules it is used in first.');
        $model->checkClassCanBeDeleted();
    }
}
