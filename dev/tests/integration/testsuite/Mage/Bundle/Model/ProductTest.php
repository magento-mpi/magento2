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
 * this test is clone of Mage_Catalog_Model_Product with product type "bundle"
 *
 * @group module:Mage_Bundle
 */
class Mage_Bundle_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Product;
        $this->_model->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);
    }

    public function testGetTypeId()
    {
        $this->assertEquals(Mage_Catalog_Model_Product_Type::TYPE_BUNDLE, $this->_model->getTypeId());
    }

    public function testGetSetTypeInstance()
    {
        // model getter
        $model = $this->_model->getTypeInstance();
        $this->assertInstanceOf('Mage_Bundle_Model_Product_Type', $model);
        $this->assertSame($model, $this->_model->getTypeInstance());

        // singleton getter
        $singleton = $this->_model->getTypeInstance(true);
        $this->assertInstanceOf('Mage_Bundle_Model_Product_Type', $singleton);
        $this->assertNotSame($model, $this->_model->getTypeInstance(true));
        $this->assertSame($singleton, $this->_model->getTypeInstance(true));

        // model setter
        $bundleModel = new Mage_Bundle_Model_Product_Type;
        $this->_model->setTypeInstance($bundleModel);
        $this->assertSame($bundleModel, $this->_model->getTypeInstance());
        $this->assertNotSame($model, $this->_model->getTypeInstance());

        // singleton setter
        $bundleSingleton = new Mage_Bundle_Model_Product_Type;
        $this->_model->setTypeInstance($bundleSingleton, true);
        $this->assertNotSame($model, $this->_model->getTypeInstance(true));
        $this->assertNotSame($bundleModel, $this->_model->getTypeInstance(true));
        $this->assertSame($bundleSingleton, $this->_model->getTypeInstance(true));
        $this->assertSame($bundleSingleton, $this->_model->getTypeInstance(true));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        Mage::app()->setCurrentStore(Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID));
        $this->_model->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_BUNDLE)
            ->setAttributeSetId(4)
            ->setName('Bundle Product')->setSku(uniqid())->setPrice(10)
            ->setMetaTitle('meta title')->setMetaKeyword('meta keyword')->setMetaDescription('meta description')
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
        ;
        $crud = new Magento_Test_Entity($this->_model, array('sku' => uniqid()));
        $crud->testCrud();
    }

    public function testGetPriceModel()
    {
        $this->_model->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);
        $type = $this->_model->getPriceModel();
        $this->assertInstanceOf('Mage_Bundle_Model_Product_Price', $type);
        $this->assertSame($type, $this->_model->getPriceModel());
    }

    public function testIsComposite()
    {
        $this->assertTrue($this->_model->isComposite());
    }
}
