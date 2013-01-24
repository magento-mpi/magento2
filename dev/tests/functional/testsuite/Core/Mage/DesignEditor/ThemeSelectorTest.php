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
     * <p>Test Theme selector page when no customization themes</p>
     *
     * @TestlinkId TL-MAGE-6478
     */
    public function testFirstEntranceWithoutVirtualThemes()
    {
        $this->loginAdminUser();
        $this->themeHelper()->deleteAllVirtualThemes();

        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('pageelement', 'header_available_themes'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'), 'Theme list not present');
        $this->assertFalse($this->controlIsPresent('pageelement', 'selector_tabs_container'), '');
    }

    /**
     * <p>Test Theme selector page when customized themes present</p>
     *
     * @TestlinkId TL-MAGE-6481
     */
    public function testFirstEntranceWithVirtualTheme()
    {
        $this->loginAdminUser();
        $this->themeHelper()->createTheme();

        $this->navigate('design_editor_selector');

        $this->assertFalse($this->controlIsPresent('pageelement', 'header_available_themes'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'selector_tabs_container'));
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

        $this->themeHelper()->deleteAllVirtualThemes();
    }

    /**
     * <p>Assign theme to default store view</p>
     * Present one store view only
     */
    public function testAssignThemeToDefaultStoreView()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->themeHelper()->deleteAllVirtualThemes();
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');

        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->waitForAjax();

        $xpathButton = sprintf($this->_getControlXpath('link', 'assign_theme'), $themeId);
        $this->waitForElementOrAlert($xpathButton);
        $this->getElement($xpathButton)->click();
        $this->alertIsPresent(
            'You are about to change this theme for your live store, are you sure want to do this?'
        );
        $this->acceptAlert();
        $this->waitForPageToLoad();
        $this->navigate('dashboard');
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * <p>Assign theme to store view</p>
     */
    public function testAssignThemeWithMultipleStoreViews()
    {
        $this->loginAdminUser();

        $this->navigate('manage_stores');
        $dataStoreView = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($dataStoreView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        $this->themeHelper()->deleteAllVirtualThemes();
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');

        $this->navigate('design_editor_selector');
        $this->waitForAjax();

        $xpathButton = sprintf($this->_getControlXpath('link', 'assign_theme'), $themeId);
        $this->waitForElementOrAlert($xpathButton);
        $this->getElement($xpathButton)->click();

        $this->assertTrue($this->controlIsPresent('pageelement', 'store_view_window'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'store_view_window'));

        $xpathStoreView = $this->_getControlXpath('pageelement', 'store_view_label_by_title');

        $xpathCustomStoreView = sprintf($xpathStoreView, $dataStoreView['store_view_name']);
        $storeViewId = $this->getElement($xpathCustomStoreView)->attribute('for');
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $storeViewName = $this->getElement($xpathStoreViewInput)->attribute('name');
        $this->fillCheckbox($storeViewName, 'Yes', $xpathStoreViewInput);


        $xpathDefaultStoreView = sprintf($xpathStoreView, 'Default Store View');

        $storeViewId = $this->getElement($xpathDefaultStoreView)->attribute('for');
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $storeViewName = $this->getElement($xpathStoreViewInput)->attribute('name');
        $this->fillCheckbox($storeViewName, 'Yes', $xpathStoreViewInput);

        $this->clickControl('button', 'store_assign_done');
        $this->waitForPageToLoad();
        $this->navigate('dashboard');
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View'));
        $this->elementIsPresent(sprintf($xpathAssignedStoreviews, $themeId, $dataStoreView['store_view_name']));

        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
    }
}
