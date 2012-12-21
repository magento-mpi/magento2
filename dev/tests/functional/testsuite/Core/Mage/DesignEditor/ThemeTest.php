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
//    /**
//     * <p>Test theme infinity scroll on page with available themes</p>
//     */
//    public function testOpenAvailableThemePage()
//    {
//        $this->loginAdminUser();
//        $this->navigate('design_editor_selector');
//        $this->waitForAjax();
//        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'));
//
//        $xpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
//        $this->waitForElementOrAlert($xpath);
//        $defaultElementsCount = $this->getControlCount('pageelement', 'theme_list_elements');
//
//        /** Check that theme list loaded */
//        $this->assertGreaterThan(0, $defaultElementsCount);
//        $xpath = $this->_getControlXpath('dropdown', 'locales_switcher');
//        $element = $this->getElement($xpath);
//        $this->focusOnElement($element);
//        $this->waitForAjax();
//
//        /**
//         * If equal to default elements count - all themes loaded
//         * If greater than default elements count - next page loaded
//         */
//        $this->assertGreaterThanOrEqual($defaultElementsCount, $this->getControlCount('pageelement', 'theme_list_elements'));
//    }
//
//    /**
//     * <p>Test theme controls</p>
//     */
//    public function testThemeControls()
//    {
//        $this->loginAdminUser();
//        $this->navigate('design_editor_selector');
//        $xpath = $this->_getControlXpath('button', 'preview_demo_button');
//        $this->waitForElement($xpath);
//
//        /**
//         * Available theme list(on first entrance)
//         */
//        $this->assertTrue($this->controlIsPresent('button', 'preview_demo_button'),
//            'Preview button is not exists');
//        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
//            'Assign button is not exists');
//
//        $this->themeHelper()->createTheme();
//        /**
//         * My customization theme
//         */
//        $this->navigate('design_editor_selector');
//
//        $this->assertTrue($this->controlIsPresent('button', 'preview_default_button'),
//            'Preview button is not exists');
//        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
//            'Assign button is not exists');
//        $this->assertTrue($this->controlIsPresent('button', 'edit_theme_button'),
//            'Edit button is not exists');
//        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
//            'Delete button is not exists');
//
//        /**
//         * Available theme list
//         */
//        $this->clickControl('link', 'available_themes_tab', false);
//        $xpath = $this->_getControlXpath('button', 'preview_default_button');
//        $this->waitForElement($xpath);
//
//        $this->assertTrue($this->controlIsPresent('button', 'preview_default_button'),
//            'Preview button is not exists');
//        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
//            'Assign button is not exists');
//        $this->assertTrue($this->controlIsPresent('button', 'edit_theme_button'),
//            'Edit button is not exists');
//
//        $this->themeHelper()->deleteAllVirtualThemes();
//    }
//
//    /**
//     * <p>Test theme selector page when customized themes present and has preview button</p>
//     */
//    public function testPreviewDefault()
//    {
//        $this->loginAdminUser();
//        $themeData = $this->themeHelper()->createTheme();
//        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
//        $this->addParameter('id', $themeId);
//
//        $this->navigate('design_editor_selector');
//        $this->waitForAjax();
//        $this->assertTrue($this->controlIsPresent('button', 'preview_default_button'),
//            'Preview button is not exists');
//        $this->clickButton('preview_default_button');
//        $this->waitForPageToLoad();
//
//        $this->assertTrue($this->controlIsPresent('pageelement', 'preview_frame'),
//            'iFrame not present in preview page');
//
//        $this->themeHelper()->deleteAllVirtualThemes();
//    }

    /**
     * <p>Test theme selector page with available themes and has preview demo button</p>
     */
    public function testPreviewDemo()
    {
        $this->loginAdminUser();
        $this->navigate('design_editor_selector');
        $this->waitForPageToLoad();
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('button', 'preview_demo_button'),
            'Preview button is not exists');
        $this->clickButton('preview_demo_button');
        $this->waitForPageToLoad();
        $this->assertTrue($this->controlIsPresent('pageelement', 'preview_frame'),
            'iFrame not present in preview page');
    }

//    /**
//     * <p>Test unassigned theme deleting</p>
//     */
//    public function testDeleteUnassignedTheme()
//    {
//        $this->loginAdminUser();
//        $this->themeHelper()->createTheme();
//        $this->navigate('design_editor_selector');
//
//        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
//            'Delete button is not exists');
//        $this->clickButtonAndConfirm('delete_theme_button', 'confirmation_for_delete');
//        $this->assertMessagePresent('success', 'success_deleted_theme');
//    }
}
