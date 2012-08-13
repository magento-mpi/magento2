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
 * Test class for Mage_Catalog_Model_Product_Attribute_Backend_Sku.
 */
class Mage_Catalog_Model_Product_Attribute_Backend_SkuTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Product_Attribute_Backend_Sku
     */
    protected $_sku;

    protected function setUp()
    {
        $this->_sku = new Mage_Catalog_Model_Product_Attribute_Backend_Sku();
        $this->_sku->setAttribute(
            Mage::getSingleton('Mage_Eav_Model_Config')->getAttribute('catalog_product', 'sku')
        );
    }

    protected function tearDown()
    {
        $this->_sku = null;
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testGenerateUniqueSkuExistingProduct()
    {
        $product = new Mage_Catalog_Model_Product();
        $product->load(1);
        $product->setId('');
        $this->assertEquals('simple', $product->getSku());
        $this->_sku->generateUniqueSku($product);
        $this->assertEquals('simple-1', $product->getSku());
    }

    /**
     * @param $product Mage_Catalog_Model_Product
     * @dataProvider uniqueSkuDataProvider
     */
    public function testGenerateUniqueSkuNotExistingProduct($product)
    {
        $this->assertEquals('simple', $product->getSku());
        $this->_sku->generateUniqueSku($product);
        $this->assertEquals('simple', $product->getSku());
    }

    public function uniqueSkuDataProvider()
    {
        $product = new Mage_Catalog_Model_Product();
        $product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->setId(1)
            ->setAttributeSetId(4)
            ->setWebsiteIds(array(1))
            ->setName('Simple Product')
            ->setSku('simple')
            ->setPrice(10)
            ->setDescription('Description with <b>html tag</b>')
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->setCategoryIds(array(2))
            ->setStockData(
            array(
                'use_config_manage_stock' => 1,
                'qty' => 100,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1,
            )
        );
        return array(array($product));
    }
}
