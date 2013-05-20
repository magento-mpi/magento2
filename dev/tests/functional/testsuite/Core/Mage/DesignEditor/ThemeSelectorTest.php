<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * <p>Theme selector tests</p>
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_DesignEditor_ThemeSelectorTest extends Mage_Selenium_TestCase
{
    /**
     * Store new window handle
     *
     * @var null
     */
    private $_windowId = null;

    /**
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Close additional browser window<p>
     */
    protected function tearDownAfterTest()
    {
        //Close 'New' browser window if any
        if ($this->_windowId) {
            $this->closeWindow($this->_windowId);
            $this->_windowId = null;
        }
        //Back to main window
        $this->window('');
    }

    /**
     * <p>Test Theme selector page when no customization themes</p>
     *
     * @TestlinkId TL-MAGE-6478
     * @test
     */
    public function firstEntranceWithoutVirtualThemes()
    {
        $this->navigate('design_editor_selector');
        $tabElement = $this->getControlElement('tab', 'my_customization');
        if ($tabElement->displayed()) {
            $this->fail('This is not first entrance. Theme customization presented.');
        }
        $this->openTab('available_themes_tab');
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'), 'Theme list not present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'header_available_themes'), 'Header is not present');
        $xpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
        $this->waitForElementOrAlert($xpath);
        $defaultElementsCount = $this->getControlCount('pageelement', 'theme_list_elements');
        /** Check that theme list loaded */
        $this->assertGreaterThan(0, $defaultElementsCount);
    }

    /**
     * <p>Assign theme to default store view</p>
     * Present one store view only
     * @TestlinkId TL-MAGE-6480
     * @test
     */
    public function assignThemeToDefaultStoreView()
    {
        //Data
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        //Steps
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        //Verify
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);

        return $themeId;
    }


    /**
     * <p>Test Theme selector page when customized themes present</p>
     *
     * @TestlinkId TL-MAGE-6481
     * @test
     * @depends assignThemeToDefaultStoreView
     */
    public function firstEntranceWithVirtualTheme($themeId)
    {
        //Data
        $this->addParameter('id', $themeId);
        //Steps
        $this->navigate('design_editor_selector');
        $tabElement = $this->getControlElement('tab', 'my_customization');
        if (!$tabElement->displayed()) {
            $this->fail($this->locationToString() . "Problem with tab 'customized_themes_tab_content':\nTab is not visible on the page");
        }
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_thumbnail'));
        $this->assertTrue($this->controlIsPresent('button', 'assign_customization_button'));
        $this->assertTrue($this->controlIsPresent('button', 'duplicate_theme'));
        $this->assertTrue($this->controlIsPresent('button', 'preview_theme_button'));

        $this->openTab('available_themes');
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'), 'Theme list not present');
//        $this->assertTrue($this->controlIsPresent('pageelement', 'header_available_themes'), 'Header is not present'); //Should be discussed with PO
        $xpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
        $this->waitForElementOrAlert($xpath);
        $defaultElementsCount = $this->getControlCount('pageelement', 'theme_list_elements');
        /** Check that theme list loaded */
        $this->assertGreaterThan(0, $defaultElementsCount);
    }

    /**
     * <p>Assign theme to store view</p>
     * @test
     */
    public function assignThemeWithMultipleStoreViews($themeTitle = 'Magento Fixed Design')
    {
        //Data
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->clickButton('reset_filter');
        $dataStoreView = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($dataStoreView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Steps
        $this->navigate('design_editor_selector');
        $this->openTab('available_themes');
        $this->waitForAjax();
        $this->addParameter('storeName', $dataStoreView['store_view_name']);
        $this->addParameter('themeTitle', $themeTitle);
        $this->designEditorHelper()->focusOnThemeElement('link', 'assign_theme');
        $this->designEditorHelper()->mouseOver('thumbnail');
        $this->clickControl('link', 'assign_theme', false);
        $this->assertTrue($this->controlIsPresent('fieldset', 'assign_theme_confirmation'));
        $this->assertTrue($this->controlIsVisible('fieldset', 'assign_theme_confirmation'));
        //Select store view for assign
        $xpathStoreView = $this->_getControlXpath('pageelement', 'store_view_label_by_title');
        $storeViewId = $this->getElement($xpathStoreView)->attribute('for');
        $this->addParameter('storeId', $storeViewId);
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $storeViewName = $this->getElement($xpathStoreViewInput)->attribute('name');
        $this->fillCheckbox($storeViewName, 'Yes', $xpathStoreViewInput);
        //Select second store view for assign
        $this->addParameter('storeName', 'Default Store View');
        $xpathDefaultStoreView = $this->_getControlXpath('pageelement', 'store_view_label_by_title');
        $storeViewId = $this->getElement($xpathDefaultStoreView)->attribute('for');
        $this->addParameter('storeId', $storeViewId);
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $storeViewName = $this->getElement($xpathStoreViewInput)->attribute('name');
        $this->fillCheckbox($storeViewName, 'Yes', $xpathStoreViewInput);
        $storeViewId = str_replace('storeview_', '', $storeViewId);
        $this->addParameter('storeId', $storeViewId);
        //Assign theme to selected store views
        $this->clickControl('button', 'assign', false);
        sleep(2);
        $this->_windowId = $this->selectLastWindow();
        $themeId = $this->defineIdFromUrl();
        $this->addParameter('id', $themeId);
        $this->validatePage('assigned_theme_in_navigation');
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->assertMessagePresent('success', 'assign_success');
        $this->clickButton('close');
        //Verify
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View'));
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, $dataStoreView['store_view_name']));
    }

    /**
     * <p>Cancel multiple assign operation</p>
     * @test
     */
    public function cancelMultipleAssignTheme($themeTitle = 'Magento Fixed Design')
    {
        //Data
        $this->navigate('manage_stores');
        $this->clickButton('reset_filter');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $dataStoreView = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($dataStoreView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Steps
        $this->navigate('design_editor_selector');
        $this->openTab('available_themes');
        $this->waitForAjax();
        $this->addParameter('themeTitle', $themeTitle);
        $this->designEditorHelper()->focusOnThemeElement('button', 'assign_theme_button');
        $this->designEditorHelper()->mouseOver('thumbnail');
        $this->clickButton('assign_theme_button');
        $this->clickButton('close');
        $this->assertFalse($this->controlIsVisible('button', 'assign_theme_button'));
        $this->addParameter('themeTitle', $themeTitle);
        $this->designEditorHelper()->mouseOver('thumbnail');
        $this->clickButton('assign_theme_button');
        $this->clickControl('link', 'close_x');
        $this->assertFalse($this->controlIsVisible('button', 'assign_theme_button'));
        //Clean after test
        $this->navigate('manage_stores');
        $this->clickButton('reset_filter');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
    }

    /**
     * Edit from Available theme tab.
     * @test
     */
    public function editFromAvailableTab($themeTitle = 'Magento Fixed Design')
    {
        //Steps
        $this->navigate('design_editor_selector');
        $this->openTab('available_themes');
        $this->waitForAjax();
        $this->addParameter('themeTitle', $themeTitle);
        $this->designEditorHelper()->focusOnThemeElement('link', 'edit_theme_button');
        $this->designEditorHelper()->mouseOver('thumbnail');
        $this->clickControl('link', 'edit_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $themeId = $this->defineIdFromUrl();
        $this->addParameter('id', $themeId);
        //Verify
        $this->validatePage('preview_theme_in_design');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'));
    }
}
