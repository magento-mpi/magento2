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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Goext Team <DL-Magento-Team-Goext@corp.ebay.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Products deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Unitprice_GeneralConfiguration_EnableUnitPriceTest
    extends Mage_Selenium_TestCase
{
    protected static $_productNameSimple;

    protected static $_productSearchSimple;

    protected static $_categoryPath;

    protected static $_unitPriceData;

    public function setUpBeforeTests()
    {
        //Extension configuration
        $this->loginAdminUser();

        //Configure Unit Price
        $this->navigate('system_configuration');
        $configData = $this->loadDataSet('ConfigUnitPrice', 'unitprice_default_sysconf');
        $this->systemConfigurationHelper()->configure($configData);

        //Create test category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        self::$_categoryPath = $this->loadDataSet('Category', 'sub_category_required');
        $this->categoryHelper()->createCategory(self::$_categoryPath);

        //Load data for simple product
        $productDataSimple = $this->loadDataSet(
            'Product', 'simple_product_with_param_for_configurable',
            array(
                'general_name' => '%randomize% simple_product_for_order',
                'prices_price' => '100',
            )
        );
        $productDataSimple['categories'] = 'Default Category'
            . '/' . $this->_getExpectedCategoryNameAfterSave(self::$_categoryPath['name']);

        self::$_productSearchSimple = $this->loadDataSet(
            'Product', 'product_search',
            array('product_sku' => $productDataSimple['general_sku'])
        );

        //Creating simple product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productDataSimple);
        self::$_productNameSimple = $productDataSimple['general_name'];
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $configData = $this->loadDataSet('ConfigUnitPrice', 'unitprice_default_sysconf');
        $this->systemConfigurationHelper()->configure($configData);
        $this->logoutAdminUser();
    }

    /**
     * @test
     * @TestlinkId TL-GOEXT-2, TL-GOEXT-3, TL-GOEXT-846
     */
    public function unitPriceEnable()
    {
        //Load data for Unit Price
        self::$_unitPriceData = $this->loadDataSet(
            'ConfigProductUnitPrice', 'unitprice_default_prod_pricetab',
            array(
                'unit_price_measure_for_base_prod_pricetab' => 'Pound (lbs)',
                'unit_price_measure_for_unit_prod_pricetab' => 'Pound (lbs)',
                'unit_price_one_item_based_prod_volume' => '10',
                'unit_price_unit_prod_volume' => '1'
            )
        );

        //Set unit Price fields for products in admin
        $this->unitpriceHelper()->setProductUnitPrice(self::$_productSearchSimple, self::$_unitPriceData);
        $this->logoutAdminUser();

        //Unit Price label
        $unitPriceLabel =
            '$10.00 / ' .
                self::$_unitPriceData['unit_price_unit_prod_volume'] .
                ' ' .
                self::$_unitPriceData['unit_price_measure_for_unit_prod_pricetab'];

        //Verify Unit price lable on frontend
        $this->unitpriceHelper()->verifyUnitPriceOnProductPage($unitPriceLabel, self::$_productNameSimple);
    }

    /**
     * @test
     * @depends unitPriceEnable
     * @TestlinkId TL-GOEXT-844
     */
    public function unitPriceDisable()
    {
        //Extension configuration
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        //$this->systemConfigurationHelper()->loadDataAndConfigure('ConfigUnitPrice', 'unitprice_disable_sysconf');
        $configData = $this->loadDataSet('ConfigUnitPrice', 'unitprice_disable_sysconf');
        $this->systemConfigurationHelper()->configure($configData);

        //Verify Unit Price is still availabe on product edit
        $this->navigate('manage_products');
        $this->unitpriceHelper()->setProductUnitPrice(self::$_productSearchSimple, self::$_unitPriceData);
        $this->logoutAdminUser();

        //Verify Unit price label on frontend are absent
        $this->unitpriceHelper()->verifyUnitPriceOnProductPage('', self::$_productNameSimple, false);
    }

    /**
     * Currently category name is truncated after 255 characters
     *
     * @param string $categoryNameForSave
     * @return string
     */
    protected function _getExpectedCategoryNameAfterSave($categoryNameForSave)
    {
        return substr($categoryNameForSave, 0, 255);
    }
}
