<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Theme
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

    /**
     * Theme management tests for Backend
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Enterprise_Mage_Theme_ThemeTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * @dataProvider allThemeCssLinks
     * @test
     */
    public function verifyThemeCssLinks($linkName, $linkQty)
    {
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => 'Magento Fixed Design',
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
                 'magento_version_from' => $this->themeHelper()->generateVersion(),
                 'magento_version_to' => $this->themeHelper()->generateVersion(),
            )
        );
        //Steps:
        $this->themeHelper()->createTheme($themeData, false);
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->openTab('css_editor');
        $existedLinks = $this->getControlCount('link', $linkName);
        $this->assertEquals($linkQty, $existedLinks, 'Incorrect quantity of theme links');
    }

    public function allThemeCssLinks()
    {
        return array(
            array('framework_files', '5'),
            array('library_files', '2'),
            array('theme_files', '7'),
        );
    }

    /**
     * <p>Notice: setup *.css in mimeType</p>
     * https://wiki.corp.x.com/display/QAA/Configure+FireFox+for+auto+confirm+any+file+types
     *
     * Download theme css files
     *
     * @param $linkName
     * @param $fileName
     * @dataProvider allThemeCss
     * @test
     */
    public function downloadThemeCss($fileName, $linkName)
    {
        //Data
        $fileUrl = $this->getConfigHelper()->getPathToTestFiles($fileName);
        $expectedContent = file_get_contents($fileUrl);
        $expectedContent = str_replace(array("\r\n", "\n"), '', $expectedContent);
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => 'Magento Fixed Design',
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
                 'magento_version_from' => $this->themeHelper()->generateVersion(),
                 'magento_version_to' => $this->themeHelper()->generateVersion(),
            )
        );
        //Steps:
        $this->themeHelper()->createTheme($themeData, false);
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->openTab('css_editor');

        $selectedFileUrl = $this->getControlAttribute('link', $linkName, 'href');
        $downloadedFileContent = $this->getFile($selectedFileUrl);
        $downloadedFileContent = str_replace(array("\r\n", "\n"), '', $downloadedFileContent);
        $this->assertEquals($expectedContent, $downloadedFileContent, 'File was not downloaded or not equal to expected.');
    }

    public function allThemeCss()
    {
        return array(
            array('Enterprise_Banner__widgets.css', 'enterprise_banner_widgets'),//0
            array('Enterprise_CatalogEvent__widgets.css', 'enterprise_catalog_event_widgets'), //1
            array('Enterprise_Cms__widgets.css', 'enterprise_cms_widgets'), //2
            array('Mage_Catalog--widgets.css', 'mage_catalog_widget'), //3
            array('Mage_Oauth--css-oauth-simple.css', 'mage_oauth_css_oauth_simple'), //4
            array('jquery_jqzoom_css_jquery.jqzoom.css', 'jquery_jqzoom_css'), //5
            array('mage-calendar.css', 'mage_calendar'), //6
            array('Enterprise_css_print.css', 'css_print'), //7
            array('Enterprise_css_styles-ie.css', 'css_style_ie'), //8
            array('Enterprise_css_styles.css', 'css_style'), //9
            array('Enterprise_Default_Cms__widgets.css', 'mage_cms_widgets'),
            array('Enterprise_Default_Page__css_tabs.css', 'mage_page_css_tabs'),
            array('Enterprise_Default_Reports__widgets.css', 'mage_reports_widgets'),
            array('Enterprise_Default_Widget__widgets.css', 'mage_widget_widgets'),
            );
    }

}