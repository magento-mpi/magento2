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
 *
 * @group module:Mage_Bundle
 * @magentoDataFixture Mage/Bundle/_files/products.php
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
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Type_Abstract', $model);
        $this->assertSame($model, $this->_model->getTypeInstance());

        // singleton getter
        $singleton = $this->_model->getTypeInstance(true);
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Type_Abstract', $singleton);
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

    public function testGetIdBySku()
    {
        $this->assertEquals(1, $this->_model->getIdBySku('bundle_product_one')); // fixture
    }

    public function testGetAttributes()
    {
        // fixture required
        $this->_model->load(1);
        $attributes = $this->_model->getAttributes();
        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('sku', $attributes);
        $this->assertInstanceOf('Mage_Catalog_Model_Resource_Eav_Attribute', $attributes['sku']);
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
        $default = $this->_model->getPriceModel();
        $this->assertInstanceOf('Mage_Catalog_Model_Product_Type_Price', $default);
        $this->assertSame($default, $this->_model->getPriceModel());

        $this->_model->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);
        $type = $this->_model->getPriceModel();
        $this->assertInstanceOf('Mage_Bundle_Model_Product_Price', $type);
        $this->assertSame($type, $this->_model->getPriceModel());
    }

    /**
     * @covers Mage_Catalog_Model_Product::getCalculatedFinalPrice
     * @covers Mage_Catalog_Model_Product::getMinimalPrice
     * @covers Mage_Catalog_Model_Product::getSpecialPrice
     * @covers Mage_Catalog_Model_Product::getSpecialFromDate
     * @covers Mage_Catalog_Model_Product::getSpecialToDate
     * @covers Mage_Catalog_Model_Product::getRequestPath
     * @covers Mage_Catalog_Model_Product::getGiftMessageAvailable
     * @covers Mage_Catalog_Model_Product::getRatingSummary
     * @dataProvider getObsoleteGettersDataProvider
     * @param string $key
     * @param string $method
     */
    public function testGetObsoleteGetters($key, $method)
    {
        $value = uniqid();
        $this->assertEmpty($this->_model->$method());
        $this->_model->setData($key, $value);
        $this->assertEquals($value, $this->_model->$method());
    }

    public function getObsoleteGettersDataProvider()
    {
        return array(
            array('calculated_final_price', 'getCalculatedFinalPrice'),
            array('minimal_price', 'getMinimalPrice'),
            array('special_price', 'getSpecialPrice'),
            array('special_from_date', 'getSpecialFromDate'),
            array('special_to_date', 'getSpecialToDate'),
            array('request_path', 'getRequestPath'),
            array('gift_message_available', 'getGiftMessageAvailable'),
            array('rating_summary', 'getRatingSummary'),
        );
    }

    public function testIsComposite()
    {
        $this->assertTrue($this->_model->isComposite());
    }
}
