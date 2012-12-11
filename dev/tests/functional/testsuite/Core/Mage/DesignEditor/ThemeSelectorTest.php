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
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'header_available_themes'));
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'theme_list'));
        $this->assertElementNotPresent($this->_getControlXpath('pageelement', 'selector_tabs_container'));
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

        $xpathCustomizationContent = $this->_getControlXpath('pageelement', 'customized_themes_tab_content');
        $xpathAvailableContent = $this->_getControlXpath('pageelement', 'available_themes_tab_content');
        $xpathThemeList = $this->_getControlXpath('pageelement', 'theme_list');

        $this->assertElementNotPresent($this->_getControlXpath('pageelement', 'header_available_themes'));
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'selector_tabs_container'));
        $this->assertElementPresent($xpathCustomizationContent);
        $this->assertVisible($xpathCustomizationContent);
        $this->assertElementPresent($xpathAvailableContent);
        $this->assertNotVisible($xpathAvailableContent);
        $this->assertNotVisible($xpathThemeList);

        $this->clickControl('link', 'available_themes_tab', false);
        $this->assertVisible($xpathAvailableContent);
        $this->assertElementPresent($xpathThemeList);
        $this->assertVisible($xpathThemeList);
        $this->assertNotVisible($xpathCustomizationContent);

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
        $this->waitForAjax();

        $xpathButton = sprintf($this->_getControlXpath('link', 'assign_theme'), $themeId);
        $this->chooseOkOnNextConfirmation();
        $this->click($xpathButton);
        $this->assertConfirmation(
            'You are about to change this theme for your live store, are you sure want to do this?'
        );
        $this->waitForAjax();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->isElementPresent($xpathAssignedStoreviews);
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
        $this->click($xpathButton);

        $xpathStoreWindow = $this->_getControlXpath('pageelement', 'store_view_window');
        $this->assertElementPresent($xpathStoreWindow);
        $this->assertVisible($xpathStoreWindow);

        $xpathStoreView = $this->_getControlXpath('pageelement', 'store_view_label_by_title');

        $xpathStoreView = sprintf($xpathStoreView, $dataStoreView['store_view_name']);
        $storeViewId = $this->getAttribute($xpathStoreView . '@for');
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $this->check($xpathStoreViewInput);

        $xpathStoreView = sprintf($xpathStoreView, 'Default Store View');
        $storeViewId = $this->getAttribute($xpathStoreView . '@for');
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $this->check($xpathStoreViewInput);

        $this->click($this->_getControlXpath('link', 'store_assign_done'));
        $this->waitForAjax();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $this->isElementPresent(sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View'));
        $this->isElementPresent(sprintf($xpathAssignedStoreviews, $themeId, $dataStoreView['store_view_name']));

        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
    }
}
