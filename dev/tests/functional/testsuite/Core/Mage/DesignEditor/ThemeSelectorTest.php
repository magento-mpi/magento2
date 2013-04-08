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
        //Delete virtual themes
        $this->themeHelper()->deleteAllVirtualThemes();
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
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'), 'Theme list not present');
//        $this->assertTrue($this->controlIsPresent('pageelement', 'header_available_themes'), 'Header not present');
    }

    /**
     * <p>Test Theme selector page when customized themes present</p>
     *
     * @TestlinkId TL-MAGE-6481
     * @test
     */
    public function firstEntranceWithVirtualTheme()
    {
        //Data
        $this->themeHelper()->createTheme();
        //Steps
        $this->navigate('design_editor_selector');
        $this->assertFalse($this->controlIsPresent('pageelement', 'header_available_themes'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'customized_themes_tab_content'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'customized_themes_tab_content'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'available_themes_tab_content'));
        $this->assertFalse($this->controlIsVisible('pageelement', 'available_themes_tab_content'));
        $this->assertFalse($this->controlIsVisible('pageelement', 'theme_list'));

        $this->clickControl('link', 'available_themes_tab', false);
        $this->assertTrue($this->controlIsVisible('pageelement', 'available_themes_tab_content'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'theme_list'));
        $this->assertFalse($this->controlIsVisible('pageelement', 'customized_themes_tab_content'));
    }

    /**
     * <p>Assign theme to default store view</p>
     * Present one store view only
     * @test
     */
    public function assignThemeToDefaultStoreView()
    {
        //Data
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->waitForAjax();
        $this->addParameter('id', $themeId);
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickControlAndConfirm('link', 'assign_theme', 'confirmation_for_assign_to_default');
        $this->_windowId = $this->selectLastWindow();
        $this->validatePage('assigned_theme_default_in_design');
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');

        //Verify
        $this->addParameter('id', $themeId);
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * <p>Assign theme to store view</p>
     * @test
     */
    public function assignThemeWithMultipleStoreViews()
    {
        //Data
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->clickButton('reset_filter');
        $dataStoreView = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($dataStoreView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->addParameter('id', $themeId);
        $this->addParameter('storeName', $dataStoreView['store_view_name']);
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickControl('link', 'assign_theme', false);

        $this->assertTrue($this->controlIsPresent('pageelement', 'store_view_window'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'store_view_window'));

        $xpathStoreView = $this->_getControlXpath('pageelement', 'store_view_label_by_title');
        $storeViewId = $this->getElement($xpathStoreView)->attribute('for');
        $this->addParameter('storeId', $storeViewId);
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $storeViewName = $this->getElement($xpathStoreViewInput)->attribute('name');
        $this->fillCheckbox($storeViewName, 'Yes', $xpathStoreViewInput);
        $this->addParameter('storeName', 'Default Store View');
        $xpathDefaultStoreView = $this->_getControlXpath('pageelement', 'store_view_label_by_title');

        $storeViewId = $this->getElement($xpathDefaultStoreView)->attribute('for');
        $this->addParameter('storeId', $storeViewId);
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $storeViewName = $this->getElement($xpathStoreViewInput)->attribute('name');
        $this->fillCheckbox($storeViewName, 'Yes', $xpathStoreViewInput);

        $this->clickControl('button', 'store_assign_done');
        $this->waitForPageToLoad();
        $this->_windowId = $this->selectLastWindow();
        $this->validatePage('assigned_theme_in_design');
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        //Verify
        $this->validatePage('design_editor_selector');
        $this->addParameter('id', $themeId);
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View'));
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, $dataStoreView['store_view_name']));
    }

    /**
     * <p>Cancel multiple assign operation</p>
     * @test
     */
    public function cancelMultipleAssignTheme()
    {
        //Data
        $this->navigate('manage_stores');
        $this->clickButton('reset_filter');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $dataStoreView = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($dataStoreView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        $themeData = $this->themeHelper()->createTheme();
        $themeData['id'] = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->addParameter('id', $themeData['id']);
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('assign_theme_button');
        $this->clickButton('store_assign_close');
        $this->assertFalse($this->buttonIsPresent('assign_theme_button'));
        $this->addParameter('id', $themeData['id']);
        $this->clickButton('assign_theme_button');
        $this->clickButton('store_assign_close_x');
        $this->assertFalse($this->buttonIsPresent('assign_theme_button'));
        //Clean after test
        $this->themeHelper()->deleteTheme($themeData);
        $this->navigate('manage_stores');
        $this->clickButton('reset_filter');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
    }

    /**
     * <p>Test theme selector page with available themes and has preview demo button</p>
     * @test
     */
    public function themeDemo()
    {
        //Data
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        $this->addParameter('id', $themeId);
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('preview_demo_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeId);
        //Verify
        $this->validatePage('preview_theme_in_navigation');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'));
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'));
    }

    /**
     * Edit from Available theme tab.
     * @test
     */
    public function editFromAvailableTab()
    {
        //Data
        $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        $this->addParameter('id', $themeId);
        //Steps
        $this->navigate('design_editor_selector');
        $this->clickControl('link', 'available_themes_tab', false);
        $this->waitForAjax();
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('edit_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeId);
        //Verify
        $this->validatePage('preview_theme_in_design');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'));
    }
}
