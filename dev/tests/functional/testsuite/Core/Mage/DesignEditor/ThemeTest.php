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
     * Preconditions:
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }
    /**
     * <p>Test theme infinity scroll on page with available themes</p>
     * @test
     */
    public function openAvailableThemePage()
    {
        $this->themeHelper()->deleteAllVirtualThemes();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_list'));

        $xpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
        $this->waitForElementOrAlert($xpath);
        $defaultElementsCount = $this->getControlCount('pageelement', 'theme_list_elements');

        /** Check that theme list loaded */
        $this->assertGreaterThan(0, $defaultElementsCount);
    }

    /**
     * <p>Test theme controls</p>
     * @test
     */
    public function themeControls()
    {
        $this->themeHelper()->deleteAllVirtualThemes();
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $this->waitForAjax();

        /**
         * Available theme list(on first entrance)
         */
        $this->assertTrue($this->controlIsPresent('button', 'preview_demo_button'),
            'Preview button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');

        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        $this->addParameter('id', $themeId);
        /**
         * My customization theme
         */
        $this->navigate('design_editor_selector');

        $this->assertTrue($this->controlIsPresent('button', 'preview_theme_button'),
            'Preview button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'edit_theme_button'),
            'Edit button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
            'Delete button is not exists');

        /**
         * Available theme list
         */
        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
        $this->clickControl('link', 'available_themes_tab', false);
        $this->waitForAjax();

        $this->assertTrue($this->controlIsPresent('button', 'preview_demo_button'),
            'Preview button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'assign_theme_button'),
            'Assign button is not exists');
        $this->assertTrue($this->controlIsPresent('button', 'edit_theme_button'),
            'Edit button is not exists');

        $this->themeHelper()->deleteTheme($themeData);
    }

    /**
     * <p>Test theme selector page when customized themes present and has preview button</p>
     * @test
     */
    public function previewCustomizedTheme()
    {
        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        $this->addParameter('id', $themeId);

        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->clickButton('preview_theme_button');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'),
            'Theme is not opened in design mode');
        $this->clickControl('link', 'quit');
        $this->validatePage('design_editor_selector');

        $this->designEditorHelper()->deleteTheme($themeData);
    }

    /**
     * <p>Test theme selector page with available themes and has preview demo button</p>
     * @test
     */
    public function themeDemo()
    {
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo');
        $this->addParameter('id', $themeId);

        $this->navigate('design_editor_selector');
        $this->waitForAjax();

        $this->designEditorHelper()->mouseOver('theme_thumbnail');
        $this->clickButton('preview_demo_button');
        $this->assertTrue($this->controlIsPresent('pageelement', 'vde_toolbar_row'),
            'Theme is not opened for preview in design mode');

        $this->clickControl('link', 'quit');
        $this->validatePage('design_editor_selector');
        $themeId = $this->themeHelper()->getThemeIdByTitle('Magento Demo - Copy #1');
        $this->navigate('design_editor_selector');
        $this->addParameter('id', $themeId);
        $this->clickButtonAndConfirm('delete_theme_button', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_deleted_theme');
    }

    /**
     * deprecated
     * <p>Test unassigned theme deleting</p>
     * @test
     */
    public function deleteUnassignedTheme()
    {
        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');

        $this->assertTrue($this->controlIsPresent('button', 'delete_theme_button'),
            'Delete button is not exists');
        $this->designEditorHelper()->deleteTheme($themeData);
    }

    /**
     * Quick Styles attributes. TBD
     * @test
     */
    public function openQuickStylesAttributes()
    {
        $themeData = $this->themeHelper()->createTheme();
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);

        $this->navigate('design_editor_selector');
        $this->clickControl('link', 'customized_themes_tab');
        $this->addParameter('id', $themeId);
        $this->clickButton('edit_theme_button');
        $this->validatePage('preview_theme_in_design');
        $this->clickControl('link', 'quick_styles_doc');
        $this->openTab('header');

    }
}
