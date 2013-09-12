<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * As far none class is present as separate bundle product,
 * this test is clone of Magento_Catalog_Model_Product with product type "bundle"
 */
class Magento_Bundle_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Catalog_Model_Product');
        $this->_model->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE);
    }

    public function testGetTypeId()
    {
        $this->assertEquals(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE, $this->_model->getTypeId());
    }

    public function testGetSetTypeInstance()
    {
        // model getter
        $typeInstance = $this->_model->getTypeInstance();
        $this->assertInstanceOf('Magento_Bundle_Model_Product_Type', $typeInstance);
        $this->assertSame($typeInstance, $this->_model->getTypeInstance());

        // singleton getter
        $otherProduct = Mage::getModel('Magento_Catalog_Model_Product');
        $otherProduct->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE);
        $this->assertSame($typeInstance, $otherProduct->getTypeInstance());

        // model setter
        $customTypeInstance = Mage::getModel('Magento_Bundle_Model_Product_Type');
        $this->_model->setTypeInstance($customTypeInstance);
        $this->assertSame($customTypeInstance, $this->_model->getTypeInstance());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        Mage::app()->setCurrentStore(Mage::app()->getStore(Magento_Core_Model_AppInterface::ADMIN_STORE_ID));
        $this->_model->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE)
            ->setAttributeSetId(4)
            ->setName('Bundle Product')->setSku(uniqid())->setPrice(10)
            ->setMetaTitle('meta title')->setMetaKeyword('meta keyword')->setMetaDescription('meta description')
            ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
        ;
        $crud = new Magento_TestFramework_Entity($this->_model, array('sku' => uniqid()));
        $crud->testCrud();
    }

    public function testGetPriceModel()
    {
        $this->_model->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE);
        $type = $this->_model->getPriceModel();
        $this->assertInstanceOf('Magento_Bundle_Model_Product_Price', $type);
        $this->assertSame($type, $this->_model->getPriceModel());
    }

    public function testIsComposite()
    {
        $this->assertTrue($this->_model->isComposite());
    }
}
