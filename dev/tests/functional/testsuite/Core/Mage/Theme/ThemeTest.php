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
class Core_Mage_Theme_ThemeTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Test for verifying main controls
     *
     * @test
     * @TestlinkId TL-MAGE-6662
     */
    public function navigation()
    {
        $this->navigate('theme_list');
        $this->validatePage('theme_list');
        $this->assertTrue($this->controlIsVisible('pageelement', 'theme_grid'), 'Theme grid table is absent');
        $this->assertTrue($this->controlIsVisible('button', 'add_new_theme'),
            'There is no "Add New Theme" button on the page');
        $this->assertTrue($this->controlIsVisible('button', 'reset_filter'),
            'There is no "Reset Filter" button on the page');
        $this->assertTrue($this->controlIsVisible('button', 'search'), 'There is no "Search" button on the page');
    }

    /**
     * Empty required fields.
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-6907
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $themeData = $this->loadDataSet('Theme', 'new_theme', array($emptyField => ''));
        //Steps
        $this->themeHelper()->createTheme($themeData);
        //Verify
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * DataProvider for empty required fields
     *
     * @return array
     */
    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('theme_parent', 'dropdown'),
            array('theme_version', 'field'),
            array('theme_title', 'field'),
        );
    }

    /**
     * Create Theme with required field only
     *
     * @test
     * @TestlinkId TL-MAGE-6663
     */
    public function createOnlyRequiredField()
    {
        //Data
        $themeData = $this->loadDataSet('Theme', 'new_theme');
        //Steps
        $this->themeHelper()->createTheme($themeData);
        //Verify
        $this->assertMessagePresent('success', 'success_saved_theme');
    }

    /**
     * Create Theme with all changed fields
     *
     * @return array
     *
     * @test
     */
    public function createWithAllFields()
    {
        //Data
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array(
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
            )
        );
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        //Steps
        $this->themeHelper()->createTheme($themeData);
        //Verify
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->themeHelper()->openTheme($searchData);
        $this->themeHelper()->verifyTheme($themeData);

        return $searchData;
    }

    /**
     * Delete virtual theme
     *
     * @params array $searchData
     *
     * @test
     * @depends createWithAllFields
     */
    public function deleteTheme($searchData)
    {
        //Steps
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($searchData);
        $this->clickButtonAndConfirm('delete_theme', 'confirmation_for_delete');
        //Verify
        $this->assertMessagePresent('success', 'success_deleted_theme');
        $theme = $this->themeHelper()->searchTheme($searchData);
        $this->assertNull($theme, 'Theme is present in grid after deleting');
    }

    /**
     * Impossibility to delete physical theme
     *
     * @test
     */
    public function deletePhysicalTheme()
    {
        //Data
        $searchData = $this->loadDataSet('Theme', 'theme_search_data');
        //Steps
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($searchData);
        //Verify
        $this->assertFalse($this->controlIsVisible('button', 'delete_theme',
            'Delete button is present, but should not'));
    }

    /**
     * Impossibility to change parent theme for created theme
     *
     * @test
     */
    public function editThemeParent()
    {
        //Data
        $themeData = $this->loadDataSet('Theme', 'new_theme', array(
            'theme_title' => $this->generate('string', 65, ':alnum:')
        ));
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']
        ));
        //Steps
        $this->themeHelper()->createTheme($themeData);
        $this->assertMessagePresent('success', 'success_saved_theme');
        //Verify
        $this->themeHelper()->openTheme($searchData);
        $this->assertFalse($this->controlIsVisible('dropdown', 'theme_parent'),
            'Parent Theme can be changed, but should not');
    }

    /**
     * Verify title of page for new theme
     *
     * @test
     */
    public function verifyThemeTitleNew()
    {
        //Steps
        $this->navigate('theme_list');
        $this->clickButton('add_new_theme');
        //Verify
        $this->validatePage('new_theme');
        $this->assertEquals('New Theme', $this->getControlAttribute('field', 'theme_title', 'value'),
            'Autogenerated value for Theme Title is not New Theme');
        $this->assertEquals('New Theme', $this->getControlAttribute('pageelement', 'page_title', 'text'),
            'Page Title is not New Theme');
    }

    /**
     * Verify prepopulated values
     *
     * @dataProvider prepopulatedValuesDataProvider
     * @test
     */
    public function verifyThemeAutogeneratedValues($fieldName, $value)
    {
        //Data
        $themeData = $this->loadDataSet('Theme', 'new_theme', array($fieldName => '%noValue%'));
        //Steps
        $this->themeHelper()->createTheme($themeData, false);
        //Verify
        $this->assertEquals($value, $this->getControlAttribute('field', $fieldName, 'value'),
            'Autogenerated values are not correct');
    }

    /**
     * DataProvider of prepopulated values for Theme fields
     *
     * @return array
     */
    public function prepopulatedValuesDataProvider()
    {
        return array(
            array('theme_version', '0.0.0.1'),
            array('theme_title', 'Copy of Magento Demo'),
        );
    }

    /**
     * Edit Theme
     *
     * @test
     */
    public function editThemeInfo()
    {
        //Data
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_title' => $this->generate('string', 65, ':alnum:')));
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        $editData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => '%noValue%',
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
            )
        );
        //Steps
        $this->themeHelper()->createTheme($themeData);
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->themeHelper()->openTheme($searchData);
        $this->themeHelper()->fillThemeGeneralTab($editData);
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_theme');
        //Verify
        $this->validatePage('edit_theme');
        $editData['theme_settings']['theme_parent'] = $themeData['theme_settings']['theme_parent'];
        $this->themeHelper()->verifyTheme($editData);
    }

    /**
     * Reset button functionality
     *
     * @return array
     *
     * @test
     */
    public function resetThemeForm()
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_title' => $this->generate('string', 65, ':alnum:')));
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        $editData = $this->loadDataSet('Theme', 'new_theme',
            array(
                 'theme_parent' => '%noValue%',
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
            )
        );
        //Steps:
        $this->themeHelper()->createTheme($themeData, false);
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->themeHelper()->fillThemeGeneralTab($editData);
        $this->clickButton('reset');
        $this->clickButton('save_theme');
        $this->assertMessagePresent('success', 'success_saved_theme');
        //Verify
        $this->themeHelper()->openTheme($searchData);
        $this->themeHelper()->verifyTheme($themeData);

        return $searchData;
    }

    /**
     * Verify quantity of theme CSS links
     *
     * @param string $linkName
     * @param int $linkQty
     * @param array $searchData
     *
     * @depends resetThemeForm
     * @dataProvider allThemeCssLinks
     * @test
     */
    public function verifyThemeCssLinks($linkName, $linkQty, $searchData)
    {
        //Steps
        $this->themeHelper()->openTheme($searchData);
        //Verify
        $this->openTab('css_editor');
        $existedLinks = $this->getControlCount('link', $linkName);
        $this->assertEquals($linkQty, $existedLinks, 'Incorrect quantity of theme links');
    }

    /**
     * DataProvider for verifying quantity of css links
     *
     * @return array
     */
    public function allThemeCssLinks()
    {
        return array(
            array('framework_files', 7),
            array('library_files', 1),
            array('theme_files', 3),
        );
    }

    /**
     * Notice: setup *.css in mimeType
     * https://wiki.corp.x.com/display/QAA/Configure+FireFox+for+auto+confirm+any+file+types
     *
     * Download theme css files
     *
     * @param string $linkName
     * @param string $fileName
     * @param array $searchData
     *
     * @test
     * @depends resetThemeForm
     * @dataProvider allThemeCss
     */
    public function downloadThemeCss($fileName, $linkName, $searchData)
    {
        //Data
        $fileUrl = $this->getConfigHelper()->getPathToTestFiles($fileName);
        $expectedContent = file_get_contents($fileUrl);
        $expectedContent = str_replace(array("\r\n", "\n"), '', $expectedContent);
        //Steps
        $this->themeHelper()->openTheme($searchData);
        $this->openTab('css_editor');
        //Verify
        $selectedFileUrl = $this->getControlAttribute('link', $linkName, 'href');
        $downloadedFileContent = $this->getFile($selectedFileUrl);
        $downloadedFileContent = str_replace(array("\r\n", "\n"), '', $downloadedFileContent);
        $this->assertEquals($expectedContent, $downloadedFileContent,
            'File was not downloaded or not equal to expected.');
    }

    /**
     * DataProvider of css links' content
     *
     * @return array
     */
    public function allThemeCss()
    {
        return array(
            array('Mage_Catalog--widgets.css', 'mage_catalog_widget'),
            array('Mage_Catalog__zoom.css', 'mage_catalog_zoom'),
            array('Mage_Cms__widgets.css', 'mage_cms_widgets'),
            array('Mage_Oauth--css-oauth-simple.css', 'mage_oauth_css_oauth_simple'),
            array('Mage_Page__css_tabs.css', 'mage_page_css_tabs'),
            array('Mage_Reports__widgets.css', 'mage_reports_widgets'),
            array('Mage_Widget__widgets.css', 'mage_widget_widgets'),
            array('mage-calendar.css', 'mage_calendar'),
            array('css_print.css', 'css_print'),
            array('css_styles-ie.css', 'css_style_ie'),
            array('css_styles.css', 'css_style'),
        );
    }

    /**
     * Delete all virtual themes
     * @test
     */
    public function deleteAllThemes()
    {
        $this->markTestIncomplete('TBD Deleting test cases creation');
        $this->navigate('theme_list');
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_grid'));
        $xpath = $this->_getControlXpath('pageelement', 'theme_grid_theme_path_empty_column');
        while ($this->elementIsPresent($xpath)) {
            $this->clickControl('pageelement', 'theme_grid_theme_path_empty_column');
            $this->clickButton('delete_theme', false);
            $this->assertTrue($this->alertIsPresent());
            $this->assertEquals('Are you sure you want to do this?', $this->alertText());
            $this->acceptAlert();
            $this->waitForPageToLoad();
            $this->assertMessagePresent('success', 'success_deleted_theme');
        }
    }
}
