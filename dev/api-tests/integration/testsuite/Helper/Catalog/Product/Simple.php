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
 * Simple product tests helper.
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */

class Helper_Catalog_Product_Simple extends Magento_Test_Webservice {

    /**
     * Load simple product fixture data
     *
     * @param string $fixtureName
     * @return array
     */
    public function loadSimpleProductFixtureData($fixtureName)
    {
        return require TEST_FIXTURE_DIR . "/_data/Catalog/Product/Simple/{$fixtureName}.php";
    }

    /**
     * Check simple product attributes
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $expectedProductData
     * @param array $skipAttributes
     * @param array $skipStockItemAttributes
     */
    public function checkSimpleAttributesData($product, $expectedProductData, $skipAttributes = array(),
        $skipStockItemAttributes = array()
    ) {
        $expectedProductData = array_diff_key($expectedProductData, array_flip($skipAttributes));

        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            if (isset($expectedProductData[$attribute])) {
                $this->assertEquals(strtotime($expectedProductData[$attribute]),
                    strtotime($product->getData($attribute)));
            }
        }

        $exclude = array_merge($dateAttributes, array('group_price', 'tier_price', 'stock_data',
            'url_key', 'url_key_create_redirect'));
        // Validate URL Key - all special chars should be replaced with dash sign
        $this->assertEquals('123-abc', $product->getUrlKey());
        $productAttributes = array_diff_key($expectedProductData, array_flip($exclude));
        foreach ($productAttributes as $attribute => $value) {
            $this->assertEquals($value, $product->getData($attribute), 'Invalid attribute "' . $attribute . '"');
        }

        if (isset($expectedProductData['stock_data'])) {
            $stockItem = $product->getStockItem();
            $expectedStock = array_diff_key($expectedProductData['stock_data'], array_flip($skipStockItemAttributes));
            foreach ($expectedStock as $attribute => $value) {
                $this->assertEquals($value, $stockItem->getData($attribute),
                    'Invalid stock_data attribute "' . $attribute . '"');
            }
        }
    }

    /**
     * Check stock item use default flags
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function checkStockItemDataUseDefault($product)
    {
        $stockItem = $product->getStockItem();
        $this->assertNotNull($stockItem);
        $fields = array('use_config_min_qty', 'use_config_min_sale_qty', 'use_config_max_sale_qty',
            'use_config_backorders', 'use_config_notify_stock_qty', 'use_config_enable_qty_inc');
        foreach ($fields as $field) {
            $this->assertEquals(1, $stockItem->getData($field), $field . ' is not set to 1');
        }
    }
}
