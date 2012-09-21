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

class Community2_Mage_Store_MultiStoreMode_PromotionsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Navigate to System -> Manage Store.</p>
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
        if (empty($isEmpty)){
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
     * <p>Catalog Price Rules page contains websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - General, Enable Single Store Mode </p>
     * <p>3.Go to Promotions - Catalog Price Rules </p>
     * <p>4.Check for Website column on the Grid. </p>
     * <p>Expected result: </p>
     * <p>Website column is displayed.</p>
     * <p>5.Click on the Add New Rule button.</p>
     * <p>6.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is displayed.</p>
     * <p>7. Repeat previous steps with disabled Single-Store Mode</p>
     * <p>Expected result:</p>
     * <p>The same result as in previous cases.</p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6269
     * @author Tatyana_Gonchar
     */
    public function verificationCatalogPriceRule($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->admin('manage_catalog_price_rules');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertTrue($this->controlIsPresent('multiselect', 'websites'),
            'There is no "Store View" selector on the page');
    }

    public function singleStoreModeEnablerDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode')
        );
    }

    /**
     * <p>Shopping Cart Price Rules page contains websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - General, Enable Single Store Mode </p>
     * <p>3.Go to Promotions - Shopping Cart Price Rules </p>
     * <p>4.Check for Website column on the Grid. </p>
     * <p>Expected result: </p>
     * <p>Website column is displayed.</p>
     * <p>5.Click on the Add New Rule button.</p>
     * <p>6.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is displayed.</p>
     * <p>7.Repeat previous steps with disabled Single-Store Mode</p>
     * <p>Expected result:</p>
     * <p>The same result as in previous cases.</p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6270
     * @author Tatyana_Gonchar
     */
    public function verificationShoppingCartPriceRule($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
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