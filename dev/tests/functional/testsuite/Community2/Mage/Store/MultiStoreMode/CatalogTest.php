<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store_MultiStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_Store_MultiStoreMode_CatalogTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test:</p>
     * <p>1. Login to backend.</p>
     * <p>2. Navigate to System -> Manage Store.</p>
     * <p>3.If there only one Store View - create one more (for enabling Multi Store Mode functionality).</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $tableXpath = $this->_getControlXpath('pageelement', 'stores_table');
        $titleRowCount = $this->getXpathCount($tableXpath . '//tr[@title]');
        $columnId = $this->getColumnIdByName('Store View Name') - 1;
        $storeViews = array();
        for ($rowId = 0; $rowId < $titleRowCount; $rowId++) {
            $storeView = $this->getTable($tableXpath . '.' . $rowId . '.' . $columnId);
            if (!in_array($storeView, array('Default Store View'))) {
                $storeViews[] = $storeView;
            }
        }
        $isEmpty = array_filter($storeViews);
        if (empty($isEmpty)) {
            $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
            $this->storeHelper()->createStore($storeViewData, 'store_view');
        }
    }

    protected function tearDownAfterTest()
    {
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p> Manage Product page </p>
     * <p> 1. Go to System - Configuration - General.</p>
     * <p> 2. Expand Single Store Mode fieldset and enable Single Store Mode </p>
     * <p> 3. Go to Catalog - Manage Products </p>
     * <p> 4. Check for Websites column on the Products grid.</p>
     * <p> Expected result:</p>
     * <p> Websites column is displayed </p>
     * <p> 5. Click on the Add Product button</p>
     * <p> 6. Go to the New Product page, Prices tab</p>
     * <p> 7. Check for Website column in Group Price and Tier Price tables</p>
     * <p> Expected result: </p>
     * <p> Column is displayed.</p>
     * <p> 8. Check Website tab on the Product Info tab set</p>
     * <p> Expected result:</p>
     * <p> Website tab is displayed.</p>
     * <p> 9. Repeat previous 3-8 steps with disabled Single-Store Mode.</p>
     * <p>Expected result: </p>
     * <p> All results are the same. </p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6326
     * @author Tatyana_Gonchar
     */
    public function verificationManageProducts($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->admin('manage_products');
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'),
            'There is no "Website" column on the page');
        $this->clickButton('add_new_product');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $this->productHelper()->fillProductSettings($productData);
        $this->assertTrue($this->controlIsPresent('tab', 'websites'),
            "'Websites' tab is not present on the page ");
        $this->openTab('prices');
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_group_price_grid_head');
        $this->assertTrue((isset($columnsName['website'])), "Group Price table not contain 'Website' column");
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_tier_price_grid_head');
        $this->assertTrue((isset($columnsName['website'])), "Tier Price table not contain 'Website' column");
    }

    public function singleStoreModeEnablerDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode')
        );
    }

    /**
     * <p> Search Terms page </p>
     * <p> 1. Go to System - Configuration - General.</p>
     * <p> 2. Expand Single Store Mode fieldset and enable Single Store Mode </p>
     * <p> 3. Go to Catalog - Search Terms</p>
     * <p> 4. Check for Store column on the Search Terms grid.</p>
     * <p> Expected result:</p>
     * <p> Store column is displayed </p>
     * <p> 5. Click on the Add New Search Term button</p>
     * <p> 6. Check for Store dropdown </p>
     * <p> Expected result: </p>
     * <p> Store dropdown is displayed.</p>
     * <p> 7. Repeat previous 3-6 steps with disabled Single-Store Mode.</p>
     * <p>Expected result: </p>
     * <p> All results are the same. </p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6325
     * @author Tatyana_Gonchar
     */
    public function verificationSearchTerms($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->admin('search_terms');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" column on the page');
        $this->clickButton('add_new_search_term');
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'),
            "'Store' dropdown is not present on the page ");
    }

    /**
     * <p> Review and Ratings page </p>
     * <p> 1. Go to System - Configuration - General.</p>
     * <p> 2. Expand Single Store Mode fieldset and enable Single Store Mode </p>
     * <p> 3. Go to Catalog - Review Ratings - Customer Reviews - Pending Reviews</p>
     * <p> 4. Check for Visible In column on the Pending Reviews grid.</p>
     * <p> Expected result:</p>
     * <p> Visible In column is displayed </p>
     * <p> 5. Go to Catalog - Review Ratings - Customer Reviews - All Reviews</p>
     * <p> 6. Check for Visible In column on the All Reviews grid. </p>
     * <p> Expected result: </p>
     * <p> Visible In column is displayed.</p>
     * <p> 7. Go to Catalog - Review Ratings - Manage Ratings</p>
     * <p> 8. Click on the Add New Rating button. </p>
     * <p> 9. Check for Visible In multiselect</p>
     * <p> Expected result: </p>
     * <p> Visible In multiselect is displayed.</p>
     * <p> 10. Repeat previous 3-9 steps with disabled Single-Store Mode.</p>
     * <p>Expected result: </p>
     * <p> All results are the same. </p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6327
     * @author Tatyana_Gonchar
     */
    public function verificationReviewRatings($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->admin('manage_pending_reviews');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is no 'Visible In' column on the page");
        $this->admin('manage_all_reviews');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is no 'Visible In' column on the page");
        $this->admin('manage_ratings');
        $this->clickButton('add_new_rating');
        $this->assertTrue($this->controlIsPresent('multiselect', 'visible_in'),
            "There is no 'Visible In' multiselect on the page");
    }

    /**
     * <p> Tags page </p>
     * <p> 1. Go to System - Configuration - General.</p>
     * <p> 2. Expand Single Store Mode fieldset and enable Single Store Mode </p>
     * <p> 3. Go to Catalog - Tags - All Tags </p>
     * <p> 4. Check for Store View column on the Manage Tags grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is displayed </p>
     * <p> 5. Click on the Add New Tag button. </p>
     * <p> 6. Check for Scope switcher</p>
     * <p> Expected result: </p>
     * <p> Scope switcher is displayed.</p>
     * <p> 7. Go to Catalog - Tags - Pending Tags. </p>
     * <p> 8. Check for Store View dropdown</p>
     * <p> Expected result: </p>
     * <p> Store View dropdown is displayed.</p>
     * <p> 9. Repeat previous 3-8 steps with disabled Single-Store Mode.</p>
     * <p>Expected result: </p>
     * <p> All results are the same. </p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6328
     * @author Tatyana_Gonchar
     */
    public function verificationTags($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->admin('all_tags');
        $this->assertTrue($this->controlIsPresent('dropdown', 'store_view'),
            "There is no 'Store View' column on the page");
        $this->clickButton('add_new_tag');
        $this->assertTrue($this->controlIsPresent('dropdown', 'switch_store'),
            "There is no 'Store Switcher' dropdown on the page");
        $this->admin('pending_tags');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is no 'Store View' column on the page");
    }

    /**
     * <p> URL Rewrite Management page </p>
     * <p> 1. Go to System - Configuration - General.</p>
     * <p> 2. Expand Single Store Mode fieldset and enable Single Store Mode </p>
     * <p> 3. Go to Catalog - URL Rewrite Management </p>
     * <p> 4. Check for Store View column on the URL Rewrite Management grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is displayed </p>
     * <p> 5. Repeat previous 3-4 steps with disabled Single-Store Mode.</p>
     * <p>Expected result: </p>
     * <p> All results are the same. </p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6329
     * @author Tatyana_Gonchar
     */
    public function verificationUrlRewrite($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->admin('url_rewrite_management');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'),
            "There is no 'Store View' column on the page");
    }
}