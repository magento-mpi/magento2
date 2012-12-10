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
     * <p>Test theme infinity scroll on page with available themes</p>
     */
    public function testOpenAvailableThemePage()
    {
        $this->loginAdminUser();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->isElementPresent('infinite_scroll');

        $xpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
        $defaultElementsCount = $this->getXpathCount($xpath);

        /** Check that theme list loaded */
        $this->assertGreaterThan(0, $defaultElementsCount);
        $this->getEval('window.scrollTo(0,Math.max(document.documentElement.scrollHeight, document.body.scrollHeight,'
            . 'document.documentElement.clientHeight));');
        $this->waitForAjax();

        /**
         * If equal to default elements count - all themes loaded
         * If greater than default elements count - next page loaded
         */
        $this->assertGreaterThanOrEqual($defaultElementsCount, $this->getXpathCount($xpath));
    }

    /**
     * <p>Test theme controls</p>
     */
    public function testThemeControls()
    {
        $this->loginAdminUser();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();

        /**
         * Available theme list(on first entrance)
         */
        $this->assertElementPresent($this->_getControlXpath('button', 'preview_demo_button'),
            'Preview button is not exists');
        $this->assertElementPresent($this->_getControlXpath('button', 'assign_theme_button'),
            'Assign button is not exists');

        $this->themeHelper()->createTheme();
        /**
         * My customization theme
         */
        $this->navigate('design_editor_selector');

        $this->assertElementPresent($this->_getControlXpath('button', 'preview_default_button'),
            'Preview button is not exists');
        $this->assertElementPresent($this->_getControlXpath('button', 'assign_theme_button'),
            'Assign button is not exists');
        $this->assertElementPresent($this->_getControlXpath('button', 'edit_theme_button'),
            'Edit button is not exists');
        $this->assertElementPresent($this->_getControlXpath('button', 'delete_theme_button'),
            'Delete button is not exists');

        /**
         * Available theme list
         */
        $this->clickControl('link', 'available_themes_tab', false);
        $this->waitForAjax();

        $this->assertElementPresent($this->_getControlXpath('button', 'preview_default_button'),
            'Preview button is not exists');
        $this->assertElementPresent($this->_getControlXpath('button', 'assign_theme_button'),
            'Assign button is not exists');
        $this->assertElementPresent($this->_getControlXpath('button', 'edit_theme_button'),
            'Edit button is not exists');

        $this->themeHelper()->deleteAllCustomizedTheme();
    }

    /**
     * <p>Test theme selector page when customized themes present and has preview button</p>
     */
    public function testPreviewDefault()
    {
        $this->loginAdminUser();
        $this->themeHelper()->createTheme();

        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->assertElementPresent($this->_getControlXpath('button', 'preview_default_button'),
            'Preview button is not exists');
        $this->click($this->_getControlXpath('button', 'preview_default_button'));
        $this->clickControl('link', 'available_themes_tab', false);
        $this->waitForPageToLoad();

        $this->assertElementPresent('//iframe[@id=\'preview-theme\']', 'iFrame not present in preview page');

        $this->themeHelper()->deleteAllCustomizedTheme();
    }

    /**
     * <p>Test theme selector page with available themes and has preview demo button</p>
     */
    public function testPreviewDemo()
    {
        $this->loginAdminUser();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->assertElementPresent($this->_getControlXpath('button', 'preview_demo_button'),
            'Preview button is not exists');
        $this->click($this->_getControlXpath('button', 'preview_demo_button'));
        $this->waitForPageToLoad();
        $this->assertElementPresent('//iframe[@id=\'preview-theme\']', 'iFrame not present in preview page');
    }

    /**
     * <p>Test unassigned theme deleting</p>
     */
    public function testDeleteUnassignedTheme()
    {
        $this->loginAdminUser();
        $this->themeHelper()->createTheme();
        $this->navigate('design_editor_selector');

        $this->assertElementPresent($this->_getControlXpath('button', 'delete_theme_button'),
            'Delete button is not exists');
        $this->clickButtonAndConfirm('delete_theme_button', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_deleted_theme');
    }
}
