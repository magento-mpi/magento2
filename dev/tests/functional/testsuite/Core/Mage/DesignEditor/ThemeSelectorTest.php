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
     * Preconditions:
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
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
        $this->assertTrue($this->controlIsPresent('pageelement', 'header_available_themes'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'), 'Theme list not present');
//        $this->assertFalse($this->controlIsPresent('pageelement', 'selector_tabs_container'), '');
    }

    /**
     * <p>Test Theme selector page when customized themes present</p>
     *
     * @TestlinkId TL-MAGE-6481
     * @test
     */
    public function firstEntranceWithVirtualTheme()
    {
        $themeData = $this->themeHelper()->createTheme();

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

        $this->designEditorHelper()->deleteTheme($themeData);
    }

    /**
     * <p>Assign theme to default store view</p>
     * Present one store view only
     * @test
     */
    public function assignThemeToDefaultStoreView()
    {
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->themeHelper()->deleteAllVirtualThemes();
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->waitForAjax();

        $this->addParameter('id', $themeId);
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickControlAndConfirm('link', 'assign_theme', 'confirmation_for_assign');
        $this->clickControl('link', 'quit');
        $this->addParameter('id', $themeId);
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
        $this->themeHelper()->deleteAllVirtualThemes();
    }

    /**
     * <p>Assign theme to store view</p>
     * @test
     */
    public function assignThemeWithMultipleStoreViews()
    {
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $dataStoreView = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($dataStoreView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        $this->themeHelper()->deleteAllVirtualThemes();
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');

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
        $this->navigate('dashboard');
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->addParameter('id', $themeId);
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View'));
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, $dataStoreView['store_view_name']));

        $this->themeHelper()->deleteAllVirtualThemes();
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
    }

    /**
     * <p>Assign theme from navigation mode</p>
     * Present one store view only
     * @test
     */
    public function assignThemeFromNavigationMode()
    {
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();

        $this->addParameter('id', $themeId);
        $this->clickButton('edit_theme_button');
        $this->clickButtonAndConfirm('assign_this_theme', 'confirmation_for_assign');
        $this->assertTrue($this->checkCurrentPage('assigned_theme_default_in_design'));
        $this->clickControl('link', 'quit');

        $this->themeHelper()->deleteTheme($themeData);
    }

    /**
     * View empty layout
     * @test
     */
    public function viewEmptyLayout()
    {
        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->addParameter('id', $themeId);
        $this->clickButton('edit_theme_button');
        $this->clickControlAndConfirm('link', 'view_layout', 'no_change');
        $this->clickControl('link', 'quit');

        $this->themeHelper()->deleteTheme($themeData);
    }

    /**
     * Check Mode switcher button
     * @test
     */
    public function checkModeSwitcherButton()
    {
        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->addParameter('id', $themeId);
        $this->clickButton('edit_theme_button');
        $this->clickButton('navigation_mode');
        $this->assertTrue($this->controlIsPresent('button', 'design_mode'));
        $this->clickButton('design_mode');
        $this->assertTrue($this->controlIsPresent('button', 'navigation_mode'));
        $this->clickControl('link', 'quit');
        $this->assertTrue($this->controlIsPresent('pageelement', 'customized_themes_tab_content'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'customized_themes_tab_content'));

        $this->themeHelper()->deleteTheme($themeData);
    }

}
