<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test product resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Products_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    protected $_origConfigValues = array();

    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        $rule = $this->getFixture('catalog_price_rule');
        if ($rule) {
            $this->deleteFixture('catalog_price_rule', true);
            Mage::getModel('catalogrule/rule')->applyAll();
        }
        parent::tearDown();
    }

    /**
     * Test get product price with and without taxes with applied catalog price rule
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @magentoDataFixture Api2/Catalog/_fixtures/catalog_price_rule.php
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
                    'regular_price' => $product->getPrice()  * (1 + $taxRate),
                    'final_price'   => $finalPrice,
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
                    'regular_price' => $product->getPrice(),
                    'final_price'   => $finalPrice,
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
     * Check if tax is applied correctly to product price. Specific tax config is applied during test
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $expectedPrices
     * @param array $config
     */
    protected function _checkTaxCalculation($product, $expectedPrices, $config)
    {
        foreach ($config as $configPath => $configValue) {
            $this->_updateAppConfig($configPath, $configValue, true, false, true);
        }

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $assertMessage = "Tested configuration: ";
        foreach ($config as $configPath => $configValue) {
            $assertMessage .= " $configPath = $configValue;";
        }

        foreach ($expectedPrices as $key => $value) {
            $this->assertTrue(isset($responseData[$key]), $key . ' not present in response.');
            $this->assertEquals($value, $responseData[$key], $assertMessage . 'key = ' .$key, 0.01);
        }
    }

    /**
     * Test unsuccessful product delete
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
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
     */
    public function testPost()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Create path to resource
     *
     * @param string $id
     * @param string $storeId
     * @return string
     */
    protected function _getResourcePath($id = null, $storeId = null)
    {
        $path = "products";
        if (!is_null($id)) {
            $path .= "/$id";
        }
        if (!is_null($storeId)) {
            $path .= "/store/$storeId";
        }
        return $path;
    }
}
