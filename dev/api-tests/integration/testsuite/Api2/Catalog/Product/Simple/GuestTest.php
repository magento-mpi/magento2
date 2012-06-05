<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Test simple product resource as guest role
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Product_Simple_GuestTest extends Api2_Catalog_Product_GuestAbstract
{
    /**
     * Test get product price with and without taxes with applied catalog price rule
     *
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @magentoDataFixture fixture/CatalogRule/Rule/catalog_price_rule.php
     * @resourceOperation product::get
     */
    public function testGetWithCatalogPriceRule()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        /** @var $rule Mage_CatalogRule_Model_Rule */
        $rule = $this->getFixture('catalog_price_rule');
        // default tax rate
        $taxRate = 0.0825;
        $finalPrice = $product->getPrice() - $rule->getDiscountAmount();
        $priceIncludesTax = Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX;
        $basedOn = Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON;

        $testConfigurations = array(
            array(
                'config' => array(
                    $priceIncludesTax => 0,
                    $basedOn => 'origin',
                ),
                'expected_prices' => array(
                    'regular_price_with_tax' => $product->getPrice()  * (1 + $taxRate),
                    'regular_price_without_tax' => $product->getPrice(),
                    'final_price_with_tax' => $finalPrice * (1 + $taxRate),
                    'final_price_without_tax' => $finalPrice
                )
            ),
            array(
                'config' => array(
                    $priceIncludesTax => 1,
                    $basedOn => 'origin',
                ),
                'expected_prices' => array(
                    'regular_price_with_tax' => $product->getPrice(),
                    'regular_price_without_tax' => $product->getPrice() / (1 + $taxRate),
                    'final_price_with_tax' => $finalPrice,
                    'final_price_without_tax' =>$finalPrice / (1 + $taxRate)
                )
            ),
        );

        foreach ($testConfigurations as $dataProvider) {
            $this->_checkTaxCalculation($product, $dataProvider['expected_prices'], $dataProvider['config']);
        }
    }

    /**
     * Test unsuccessful product delete
     *
     * @magentoDataFixture testsuite/Api/SalesOrder/_fixture/product_simple.php
     * @resourceOperation product::delete
     */
    public function testDelete()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful product create
     *
     * @resourceOperation product::create
     */
    public function testPost()
    {
        $productData = $this->_loadSimpleProductFixtureData('simple_product_data');
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test successful product collection get
     *
     * @magentoDataFixture fixture/Catalog/Product/products_collection.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product::multiget
     */
    public function testCollectionGet()
    {
        $this->_reindexPrices();
        /** @var $simpleProduct Mage_Catalog_Model_Product */
        $simpleProduct = $this->getFixture('product_simple');
        // quantity of enabled visible products
        $expectedProductsCount = 2;
        $expectedData = array_merge($simpleProduct->getData(), array(
            'is_saleable' => 1,
            'regular_price_with_tax' => 99.95,
            'regular_price_without_tax' => 99.95,
            'final_price_with_tax' => 99.95,
            'final_price_without_tax' => 99.95
        ));
        $this->_checkProductCollectionGet($expectedProductsCount, $expectedData, 2);
    }

    /**
     * Test successful product collection get with specified store
     *
     * @magentoDataFixture fixture/Catalog/Product/products_collection.php
     * @resourceOperation product::multiget
     */
    public function testCollectionGetFromSpecifiedStore()
    {
        // prepare product with different field values on different stores
        $originalProducts = $this->getFixture('products');
        /** @var $firstProduct Mage_Catalog_Model_Product */
        $firstProduct = reset($originalProducts);
        $firstProductDefaultValues = array_merge($firstProduct->getData(), array(
            'is_saleable' => 1,
            'regular_price_with_tax' => 15.5,
            'regular_price_without_tax' => 15.5,
            'final_price_with_tax' => 15.2,
            'final_price_without_tax' => 15.2
        ));

        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store_on_new_website');
        $firstProduct->load($firstProduct->getId());
        $firstProduct->setStoreId($store->getId());
        $productDataForUpdate = $this->_loadSimpleProductFixtureData('simple_product_update_data');
        unset($productDataForUpdate['type_id']);
        unset($productDataForUpdate['attribute_set_id']);
        unset($productDataForUpdate['stock_data']);
        $firstProduct->addData($productDataForUpdate);
        $firstProduct->setData('tier_price', array(
            array('website_id' => 0,'cust_group' => 0, 'price_qty' => 5.5, 'price' => 11.054)));
        $firstProduct->save();

        $this->_reindexPrices();
        // test collection get from specific store
        $firstProductDataAfterUpdate = array_merge($firstProduct->getData(), array(
            'is_saleable' => 1,
            'regular_price_with_tax' => 15.5,
            'regular_price_without_tax' => 15.5,
            'final_price_with_tax' => 15.5,
            'final_price_without_tax' => 15.5
        ));
        // quantity of enabled visible products
        $expectedProductsCount = 1;
        $this->_checkProductCollectionGet($expectedProductsCount, $firstProductDataAfterUpdate, 1, $store->getCode());
        // test collection get with default values
        $globalAttributes = array('is_in_stock');
        foreach ($globalAttributes as $globalAttribute) {
            $firstProductDefaultValues[$globalAttribute] = $firstProductDataAfterUpdate[$globalAttribute];
        }
        $this->_checkProductCollectionGet($expectedProductsCount, $firstProductDefaultValues, 1);
    }

    /**
     * Test unsuccessful get using invalid store code
     *
     * @resourceOperation product::multiget
     */
    public function testCollectionGetFromInvalidStore()
    {
        $restResponse = $this->callGet($this->_getResourcePath(null, 'INVALID_STORE'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
    }
}
