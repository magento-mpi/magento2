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
 * Test theme list
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_DesignEditor_ThemeTest extends Mage_Selenium_TestCase
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
     * <p>Test theme controls</p>
     * @test
     */
    public function themeControls()
    {
        /**
         * Available theme list(on first entrance)
         */
        $this->navigate('design_editor_selector');
        $this->openTab('available_themes');
        $this->waitForAjax();
        $themeId = $this->getControlElement('pageelement', 'first_theme_thumbnail')->attribute('id');
        $this->addParameter('themeId', $themeId);
        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');

        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->addParameter('id', $themeId);
        /**
         * My customization theme
         */
        $this->navigate('design_editor_selector');

        $this->assertTrue($this->controlIsPresent('button', 'preview_theme_button'),
            'Preview button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'assign_customization_button'),
            'Assign button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'edit_customization_button'),
            'Edit button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
            'Delete button is not exists');
//        $this->assertTrue($this->controlIsPresent('button', 'duplicate_theme'), //Commented due to bug MAGETWO-9692
//            'Duplicate button is not exists');

        /**
         * Available theme list
         */
        $this->clickControl('link', 'available_themes_tab', false);
        $this->waitForAjax();

        $themeId = $this->getControlElement('pageelement', 'first_theme_thumbnail')->attribute('id');
        $this->addParameter('themeId', $themeId);
        $this->designEditorHelper()->mouseOver('thumbnail');

        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');
        $this->assertTrue($this->controlIsPresent('link', 'edit_theme'),
            'Edit button is not exists');

        return $themeId;
    }

    /**
     * <p>Test theme selector page when customized themes present and has preview button</p>
     * @TestlinkId TL-MAGE-6482
     * @test
     */
    public function previewAssignedCustomizedTheme()
    {
        //Data
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->openTab('my_customization');
        $this->addParameter('id', $themeId);
        $this->clickButton('preview_theme_button');
        sleep(2);
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeId);
        //Verify
        $this->validatePage('preview_theme_in_navigation');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'),
            'Theme is not opened in navigation mode');
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
    }

    /**
     * Preview not assigned customized theme
     *
     * @test
     */
    public function previewNotAssignedCustomizedTheme()
    {
        //Data
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->designEditorHelper()->assignFromAvailableThemeTab();
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->openTab('my_customization');
        $this->addParameter('id', $themeId);
        $this->designEditorHelper()->focusOnThemeElement('button', 'preview_theme_button');
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('preview_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeId);
        //Verify
        $this->validatePage('preview_theme_in_navigation');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'),
            'Theme is not opened in design mode');
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
    }

    /**
     * <p>Test unassigned theme deleting</p>
     * @TestlinkId TL-MAGE-6922
     * @test
     */
    public function deleteUnassignedTheme()
    {
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $assignedThemeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        //Verify
        $this->addParameter('id', $assignedThemeId);
        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
            'Delete button is not exists');
        $this->designEditorHelper()->deleteTheme();
    }

    /**
     * Rename theme
     * @TestlinkId TL-MAGE-6545
     * @test
     */
    public function renameTheme()
    {
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->clickControl('pageelement', 'edit_theme_name');
        $uimapPage = $this->getUimapPage('admin', 'design_editor_selector');
        $locator = $this->_getControlXpath('field', 'theme_name', $uimapPage);
        $this->fillField('theme_name', 'renamed_theme', $locator);
        $this->addParameter('id', $themeId);
        $this->clickButton('save_rename_theme');
        $this->assertTrue($this->textIsPresent('renamed_theme'));
    }

    /**
     * Duplicate theme
     * @TestlinkId TL-MAGE-6939
     * @test
     */
    public function duplicateAssignedTheme($newName = 'renamed_for_duplicate')
    {
//        $this->markTestIncomplete('MAGETWO-9692');
        //Steps
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $this->clickControl('pageelement', 'edit_theme_name');
        $uimapPage = $this->getUimapPage('admin', 'design_editor_selector');
        $locator = $this->_getControlXpath('field', 'theme_name', $uimapPage);
        $this->fillField('theme_name', $newName, $locator);
        $this->addParameter('id', $themeId);
        $this->clickButton('save_rename_theme');
        //Verify
        $this->assertTrue($this->controlIsPresent('button', 'duplicate_theme'),
            'Duplicate button is not exists');
        $this->clickButton('duplicate_theme');
        $themeTitle = "Copy of [$newName]";
        $this->assertTrue($this->textIsPresent($themeTitle));
        $this->addParameter('themeTitle', $themeTitle);
        $this->clickButtonAndConfirm('delete_customization_button', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_deleted_theme');
    }

    /**
     * Duplicate theme
     * @TestlinkId TL-MAGE-6940
     * @test
     */
    public function duplicateCustomization($newName = 'renamed_for_duplicate')
    {
        //Steps
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->clickControl('pageelement', 'edit_theme_name');
        $uimapPage = $this->getUimapPage('admin', 'design_editor_selector');
        $locator = $this->_getControlXpath('field', 'theme_name', $uimapPage);
        $this->fillField('theme_name', $newName, $locator);
        $this->addParameter('id', $themeId);
        $this->clickButton('save_rename_theme');
        $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        //Verify
        $this->assertTrue($this->controlIsPresent('button', 'duplicate_theme'),
            'Duplicate button is not exists');
        $this->clickButton('duplicate_theme');
        $themeTitle = "Copy of [$newName]";
        $this->assertTrue($this->textIsPresent($themeTitle));
        $this->addParameter('themeTitle', $themeTitle);
        $this->clickButtonAndConfirm('delete_customization_button', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_deleted_theme');
    }

    /**
     * !!!!Mode switcher will be changed to Layout toggle!!!!
     * Check Mode switcher button and Theme preview button.
     * @TestlinkId TL-MAGE-6482
     * @test
     */
    public function checkModeSwitcher()
    {
        //Data
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->designEditorHelper()->assignFromAvailableThemeTab();
        //Steps
        $this->navigate('design_editor_selector');
        $this->addParameter('id',  $themeId);
        $this->designEditorHelper()->focusOnThemeElement('button', 'preview_theme_button');
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('preview_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id',  $themeId);
        $this->validatePage('preview_theme_in_navigation');
        //Verify
        $this->assertTrue($this->controlIsPresent('pageelement', 'mode_switcher'));
        $this->designEditorHelper()->selectModeSwitcher('Enabled');
        $this->validatePage('preview_theme_in_design');
        $this->assertTrue($this->controlIsPresent('pageelement', 'mode_switcher'));
        $this->designEditorHelper()->selectModeSwitcher('Disabled');
        $this->validatePage('preview_theme_in_navigation');
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->assertTrue($this->controlIsPresent('pageelement', 'customized_themes_tab_content'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'customized_themes_tab_content'));
    }

    /**
     * <p>Assign theme from navigation mode</p>
     * Present one store view only
     * @test
     */
    public function assignThemeFromNavigationMode()
    {
        //Data
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->designEditorHelper()->assignFromAvailableThemeTab();

        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified();
        //Steps
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->addParameter('id', $themeId);
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('preview_theme_button');
        sleep(2);
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeId);
        $this->validatePage('preview_theme_in_navigation');
        $this->clickButton('select');
        $this->clickButton('save_and_assign');
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'assign_theme_confirmation');
        $this->assertTrue($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'confirmation_for_assign_to_default'));
        $this->clickButton('save', false);
        $this->assertMessagePresent('success', 'assign_success');
        $this->clickButton('close', false);
        //Verify
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->navigate('design_editor_selector');
        $this->addParameter('id', $themeId);
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * !!!!Should be refactored according new Layout mode toggle functionality!!!
     * <p>Assign physical theme from navigation mode</p>
     * Present one store view only
     * @TestlinkId TL-MAGE-6547
     * @TestlinkId TL-MAGE-6883
     * @t est
     */
    public function assignPhysicalThemeFromNavigationMode($themeTitle = 'Plushe')
    {
        $this->markTestIncomplete('MAGETWO-9010');
        //Data
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified();
        //Steps
        $this->navigate('design_editor_selector');
        $this->openTab('available_themes');
        $this->waitForAjax();
        $this->addParameter('themeTitle', $themeTitle);
        $this->designEditorHelper()->focusOnThemeElement('link', 'edit_theme');
        $this->designEditorHelper()->mouseOver('thumbnail');
        $this->clickControl('link', 'edit_theme');
        $this->_windowId = $this->selectLastWindow();
        $themeId = $this->defineParameterFromUrl('theme_id', $url = null);
        $this->addParameter('id', $themeId);
        $this->validatePage('preview_theme_in_design');
        $this->designEditorHelper()->selectModeSwitcher('Disabled');
        $this->validatePage('preview_theme_in_navigation');
        $this->addParameter('id', $themeId);
        $this->clickControlAndConfirm('pageelement', 'assign', 'confirmation_for_assign_to_default_in_nm', false);
        $themeId = $this->defineParameterFromUrl('theme_id', $url = null);
        $this->addParameter('id', $themeId);
        $this->validatePage('assigned_theme_default_in_navigation');
        //Verify
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->navigate('design_editor_selector');
        $this->addParameter('id', $themeId);
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * <p>Assign physical theme from design mode</p>
     * Present one store view only
     * @t est
     */
    public function assignPhysicalThemeFromDesignMode($themeTitle = 'Plushe')
    {
        //Data
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        //Steps
        $this->navigate('design_editor_selector');
        $this->openTab('available_themes');
        $this->waitForAjax();
        $this->addParameter('themeTitle', $themeTitle);
        $this->designEditorHelper()->focusOnThemeElement('link', 'edit_theme');
        $this->designEditorHelper()->mouseOver('thumbnail');
        $this->clickControl('link', 'edit_theme');
        sleep(2);
        $this->_windowId = $this->selectLastWindow();
        $themeId = $this->defineParameterFromUrl('theme_id', $url = null);
        $this->addParameter('id', $themeId);
        $this->validatePage('preview_theme_in_design');
        $this->addParameter('id', $themeId);
        $this->clickButton('select');
        $this->clickButton('save_and_assign');
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'assign_theme_confirmation');
        $this->assertTrue($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'confirmation_for_assign_to_default'));
        $this->clickButton('save', false);
        $this->assertMessagePresent('success', 'assign_success');
        $this->clickButton('close', false);
        //Verify
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * <p>Assign theme from design mode</p>
     * Present one store view only
     * @TestlinkId TL-MAGE-6548
     * @TestlinkId TL-MAGE-6909
     * @TestlinkId TL-MAGE-6920
     * @test
     */
    public function assignThemeFromDesignMode()
    {
        //Data
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->designEditorHelper()->assignFromAvailableThemeTab();

        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified();
        //Steps
        $this->navigate('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->designEditorHelper()->focusOnThemeElement('button', 'edit_customization_button');
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('edit_customization_button');
        sleep(2);
        $this->_windowId = $this->selectLastWindow();
        $themeId = $this->defineParameterFromUrl('theme_id', $url = null);
        $this->addParameter('id', $themeId);
        $this->validatePage('preview_theme_in_design');
        $this->clickButton('select');
        $this->clickButton('save_and_assign');
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'assign_theme_confirmation');
        $this->assertTrue($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'confirmation_for_assign_to_default'));
        $this->clickButton('save', false);
        $this->assertMessagePresent('success', 'assign_success');
        $this->clickButton('close', false);
        //Verify
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * <p>Assign theme from My Customization tab</p>
     * @TestlinkId TL-MAGE-6491
     * @test
     */
    public function assignThemeFromCustomizeTab()
    {
        //Data
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        $this->navigate('design_editor_selector');
        $this->designEditorHelper()->assignFromAvailableThemeTab();

        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified();
        //Steps
        $this->navigate('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->clickButtonAndConfirm('assign_customization_button', 'confirmation_for_assign_to_default');
        $this->_windowId = $this->selectLastWindow();
        $this->validatePage('assigned_theme_default_in_navigation');
        //Verify
        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $xpathAssignedStoreviews = $this->_getControlXpath('pageelement', 'theme_assigned_storeview');
        $xpathAssignedStoreviews = sprintf($xpathAssignedStoreviews, $themeId, 'Default Store View');
        $this->elementIsPresent($xpathAssignedStoreviews);
    }

    /**
     * <p>Check Quick Styles attributes</p>
     * @TestlinkId TL-MAGE-6478
     * @test
     */
    public function openAndCheckQuickStylesAttributes()
    {
        //Data
        $this->navigate('design_editor_selector');
        $themeId = $this->designEditorHelper()->assignFromAvailableThemeTab();
        //Steps
        $this->addParameter('id', $themeId);
        $this->clickControl('link', 'edit_theme');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeId);
        $this->validatePage('preview_theme_in_design');
        $this->clickControl('link', 'quick_styles_doc');
        //Verify
        $this->openTab('header');
        $this->assertTrue($this->controlIsPresent('pageelement', 'background_image'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'background_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_links_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'header_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'header_links_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_handle'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'search_field'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'search_field_stroke'));

        $this->openTab('background');
        $this->assertTrue($this->controlIsPresent('pageelement', 'page_background_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'page_background_image'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_second_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_second_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_field_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'form_field_stroke_clicked'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'image_stroke_keylines'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'scroll_bar_handle'));

        $this->openTab('buttons_icons');
        $this->assertTrue($this->controlIsPresent('pageelement', 'radio_button_checkboxes_icon'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'radio_button_checkboxes_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'radio_button_checkboxes_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'button_background'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'button_text'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'button_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'icons'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'icons_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'icons_second'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'accents'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'size_swatches'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'size_swatches_unav_size'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'size_swatches_hover_selected_stroke'));

        $this->openTab('tips_messages');
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_text'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_second_text'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_second_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'tooltip_second_stroke'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'error_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'error_icon'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'success_box'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'success_icon'));

        $this->openTab('fonts');
        $this->assertTrue($this->controlIsPresent('pageelement', 'baner_text_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'page_heading_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'menu_color'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'baner_text_font'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'page_heading_font'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'menu_font'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'body_text_color'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'buttons_color'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'body_text_font'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'buttons_font'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links_hover'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links_active'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'text_links_product_name'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'small_links'));
        $this->assertTrue($this->controlIsPresent('pageelement', 'small_links_hover'));

        $this->closeWindow($this->_windowId);
        $this->_windowId = null;
        $this->selectLastWindow();
        $this->validatePage('design_editor_selector');
    }

    /**
     * Download theme css
     *
     * @param $linkName
     * @param $fileName
     * @dataProvider allThemeCss
     * @t est
     */
    public function downloadCss($fileName, $linkName)
    {
        $this->markTestIncomplete('Problem with define correct tab');

        //Data
        $themeData = $this->loadDataSet('Theme', 'all_fields');
        $this->themeHelper()->createTheme($themeData);
        $themeData['id'] = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        //Steps
        $this->navigate('design_editor_selector');
        $this->clickControl('link', 'customized_themes_tab');
        $this->addParameter('id', $themeData['id']);
        $this->clickButton('edit_theme_button');
        $this->_windowId = $this->selectLastWindow();
        $this->addParameter('id', $themeData['id']);
        $this->validatePage('preview_theme_in_design');
        $this->clickControl('link', 'code_doc');

        $this->openTab('header');
        $this->openTab('css');
        $this->clickControl('link', $linkName, false);

        $appConfig = $this->getApplicationConfig();
        if (!array_key_exists('downloadDir', $appConfig)) {
            $this->fail('downloadDir is not set in application config');
        }
        $downloadDir  = $appConfig['downloadDir'];
        $filePath = $downloadDir . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)){
            sleep(2);
            $this->assertTrue(file_exists($filePath), 'File was not downloaded');
        } else {
            $this->assertTrue(file_exists($filePath), 'File was not downloaded');
        }
    }

    public function allThemeCss()
    {
        return array(
            array('Mage_Catalog--widgets.css', 'mage_catalog_download'),
//            array('Mage_Oauth--css_oauth-simple.css', 'mage_oauth_download'),
//            array('mage_calendar.css', 'calendar_css_download'),
//            array('css_print.css', 'css_print_download'),
//            array('css_styles-ie.css', 'css_style_ie_download'),
//            array('css_styles.css', 'css_style_download'),
//            array('Mage_Cms--widgets.css', 'mage_cms_download'),
//            array('Mage_Page__css_tabs.css', 'mage_page_download'),
//            array('Mage_Reports__widgets.css', 'mage_reports_download'),
//            array('Mage_Widget__widgets.css', 'mage_widget_download')
        );
    }
}
