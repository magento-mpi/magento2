<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store_DisableSingleStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_Store_DisableSingleStoreMode_PromotionsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test:</p>
     * <p>1. Login to backend.</p>
     * <p>2. Navigate to System -> Manage Store.</p>
     * <p>3. Verify that one store-view is created.</p>
     * <p>4. Go to System - Configuration - General and disable Single-Store Mode.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Catalog Price Rules page contains websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Catalog Price Rules
     * <p>2.Check for Website column on the Grid.
     * <p>Expected result: </p>
     * <p>Website column is displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6262
     * @author Tatyana_Gonchar
     */
    public function verificationCatalogPriceRule()
    {
        $this->admin('manage_catalog_price_rules');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertTrue($this->controlIsPresent('multiselect', 'websites'),
            'There is no "Store View" selector on the page');
    }

    /**
     * <p>Shopping Cart Price Rules page contains websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Shopping Cart Price Rules
     * <p>2.Check for Website column on the Grid.
     * <p>Expected result: </p>
     * <p>Website column is displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is displayed.</p>
     *
     * @test.
     * @TestlinkId TL-MAGE-6263
     * @author Tatyana_Gonchar
     */
    public function verificationShoppingCartPriceRule()
    {
        $this->admin('manage_shopping_cart_price_rules');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertTrue($this->controlIsPresent('multiselect', 'websites'),
            'There is no "Store View" selector on the page');
    }
}