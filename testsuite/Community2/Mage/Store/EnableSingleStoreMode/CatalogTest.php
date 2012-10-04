<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store_EnableSingleStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_Store_EnableSingleStoreMode_CatalogTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test:</p>
     * <p>1. Login to backend.</p>
     * <p>2. Navigate to System -> Manage Store.</p>
     * <p>3. Verify that one store-view is created.</p>
     * <p>4. Go to System - Configuration - General and enable Single-Store Mode.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $config = $this->loadDataSet('SingleStoreMode', 'enable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    protected function tearDownAfterTest()
    {
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p> Manage Product page </p>
     * <p> 1. Go to Catalog - Manage Products </p>
     * <p> 2. Check for Websites column on the Products grid.</p>
     * <p> Expected result:</p>
     * <p> Websites column is not displayed </p>
     * <p> 3. Click on the Add Product button</p>
     * <p> 4. Go to the New Product page, Prices tab</p>
     * <p> 5. Check for Website column in Group Price and Tier Price tables</p>
     * <p> Expected result: </p>
     * <p> Column is not displayed.</p>
     * <p> 6. Check Website tab on the Product Info tab set</p>
     * <p> Expected result:</p>
     * <p> Website tab is not displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6317
     * @author Tatyana_Gonchar
     */
    public function verificationManageProducts()
    {
        //Steps
        $this->admin('manage_products');
        //Verifying
        $this->assertFalse($this->controlIsPresent('dropdown', 'website'),
            'There is "Website" column on the page');
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productData);
        //Veryfying
        $this->assertFalse($this->controlIsPresent('tab', 'websites'),
            "'Websites' tab is present on the page ");
        //Steps
        $this->openTab('prices');
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_group_price_grid_head');
        //Veryfying
        $this->assertFalse((isset($columnsName['website'])), "Group Price table contains 'Website' column");
        //Steps
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_tier_price_grid_head');
        //Veryfying
        $this->assertFalse((isset($columnsName['website'])), "Tier Price table contains 'Website' column");
    }

    /**
     * <p> Search Terms page </p>
     * <p> 1. Go to Catalog - Search Terms</p>
     * <p> 2. Check for Store column on the Search Terms grid.</p>
     * <p> Expected result:</p>
     * <p> Store column is not displayed </p>
     * <p> 3. Click on the Add New Search Term button</p>
     * <p> 4. Check for Store dropdown </p>
     * <p> Expected result: </p>
     * <p> Store dropdown is not displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6316
     * @author Tatyana_Gonchar
     */
    public function verificationSearchTerms()
    {
        //Steps
        $this->admin('search_terms');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            'There is "Store" column on the page');
        //Steps
        $this->clickButton('add_new_search_term');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'),
            "'Store' dropdown is present on the page ");
    }

    /**
     * <p> Review and Ratings page </p>
     * <p> 1. Go to Catalog - Review Ratings - Customer Reviews - Pending Reviews</p>
     * <p> 2. Check for Visible In column on the Pending Reviews grid.</p>
     * <p> Expected result:</p>
     * <p> Visible In column is not displayed </p>
     * <p> 3. Go to Catalog - Review Ratings - Customer Reviews - All Reviews</p>
     * <p> 4. Check for Visible In column on the All Reviews grid. </p>
     * <p> Expected result: </p>
     * <p> Visible In column is not displayed.</p>
     * <p> 5. Go to Catalog - Review Ratings - Manage Ratings</p>
     * <p> 6. Click on the Add New Rating button. </p>
     * <p> 7. Check for Visible In multiselect</p>
     * <p> Expected result: </p>
     * <p> Visible In multiselect is not displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6318
     * @author Tatyana_Gonchar
     */
    public function verificationReviewRatings()
    {
        //Steps
        $this->admin('manage_pending_reviews');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is 'Visible In' column on the page");
        //Steps.
        $this->admin('manage_all_reviews');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is 'Visible In' column on the page");
        //Steps
        $this->admin('manage_ratings');
        $this->clickButton('add_new_rating');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('multiselect', 'visible_in'),
            "There is 'Visible In' multiselect on the page");
    }

    /**
     * <p> Tags page </p>
     * <p> 1. Go to Catalog - Tags - All Tags </p>
     * <p> 2. Check for Store View column on the Manage Tags grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is not displayed </p>
     * <p> 3. Click on the Add New Tag button. </p>
     * <p> 4. Check for Scope switcher</p>
     * <p> Expected result: </p>
     * <p> Scope switcher is not displayed.</p>
     * <p> 5. Go to Catalog - Tags - Pending Tags. </p>
     * <p> 6. Check for Store View dropdown</p>
     * <p> Expected result: </p>
     * <p> Store View dropdown is not displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6319
     * @author Tatyana_Gonchar
     */
    public function verificationTags()
    {
        //Steps
        $this->admin('all_tags');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'store_view'),
            "There is 'Store View' column on the page");
        //Steps
        $this->clickButton('add_new_tag');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'switch_store'),
            "There is 'Store Switcher' dropdown on the page");
        //Steps
        $this->admin('pending_tags');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is 'Store View' column on the page");
    }

    /**
     * <p> URL Rewrite Management page </p>
     * <p> 1. Go to Catalog - URL Rewrite Management </p>
     * <p> 2. Check for Store View column on the URL Rewrite Management grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is not displayed </p>
     *
     * @test
     * @TestlinkId TL-MAGE-6320
     * @author Tatyana_Gonchar
     */
    public function verificationUrlRewrite()
    {
        //Steps
        $this->admin('url_rewrite_management');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            "There is 'Store View' column on the page");
    }
}