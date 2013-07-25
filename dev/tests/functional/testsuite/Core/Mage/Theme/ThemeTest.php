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
     *
     * @test
     */
    public function navigation()
    {
        $this->navigate('theme_list');
        $this->validatePage('theme_list');
        $this->assertTrue($this->controlIsVisible('pageelement', 'theme_grid'), 'Theme grid table is not present');
        $this->assertTrue($this->controlIsVisible('button', 'add_new_theme'),
            'There is no "Add New Theme" button on the page');
        $this->assertTrue($this->controlIsVisible('button', 'reset_filter'),
            'There is no "Reset Filter" button on the page');
        $this->assertTrue($this->controlIsVisible('button', 'search'), 'There is no "Search" button on the page');
    }

    /**
     * Empty required fields.
     *
     * @param $emptyField
     * @param $fieldType
     * @dataProvider withRequiredFieldsEmptyDataProvider
     *
     * @test
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'new_theme', array($emptyField => ''));
        //Steps:
        $this->themeHelper()->createTheme($themeData);
        //Verify:
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('theme_parent', 'dropdown'),
            array('theme_version', 'field'),
            array('theme_title', 'field'),
            array('magento_version_from', 'field'),
            array('magento_version_to', 'field'),
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
        $themeData = $this->loadDataSet('Theme', 'new_theme', array('theme_parent' => 'Recon'));
        //Steps
        $this->themeHelper()->createTheme($themeData);
        //Verify
        $this->assertMessagePresent('success', 'success_saved_theme');
    }

    /**
     * Create Theme with all changed fields
     *
     * @test
     */
    public function createWithAllFields()
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => 'Umecha',
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
                 'magento_version_from' => $this->themeHelper()->generateVersion(),
                 'magento_version_to' => $this->themeHelper()->generateVersion(),
            )
        );
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        //Steps:
        $this->themeHelper()->createTheme($themeData);
        //Verify:
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->themeHelper()->openTheme($searchData);
        $this->themeHelper()->verifyTheme($themeData);

        return $themeData;
    }

    /**
     * Delete virtual theme
     *
     * @depends createWithAllFields
     * @params $themeData
     * @test
     */
    public function deleteTheme($themeData)
    {
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($searchData);
        $this->clickButtonAndConfirm('delete_theme', 'confirmation_for_delete');
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
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => 'Piece of Cake'));
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($searchData);
        $this->assertFalse($this->controlIsVisible('button', 'delete_theme'));
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
            'theme_parent' => 'Recon',
            'theme_title' => $this->generate('string', 65, ':alnum:')
            ));
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        //Steps
        $this->themeHelper()->createTheme($themeData);
        $this->assertMessagePresent('success', 'success_saved_theme');
        //Verify
        $this->themeHelper()->openTheme($searchData);
        $this->assertFalse($this->elementIsPresent("//select[@id='parent_id']"),
            'Parent Theme can be changed, but should not');
    }

    /**
     * Verify title of page for new theme
     *
     * @test
     */
    public function verifyThemeTitleNew()
    {
        $this->navigate('theme_list');
        $this->clickButton('add_new_theme');
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
        //Data:
        $themeData = $this->loadDataSet('Theme', 'new_theme', array($fieldName => '%noValue%'));
        //Steps:
        $this->themeHelper()->createTheme($themeData, false);
        $this->clickButton('save_and_continue_edit');
        $this->assertEquals($value, $this->getControlAttribute('field', $fieldName, 'value'),
            'Autogenerated values are not correct');
    }

    public function prepopulatedValuesDataProvider()
    {
        return array(
            array('theme_version', '0.0.0.1'),
            array('theme_title', 'Copy of Upstream'),
            array('magento_version_from', '2.0.0.0-dev1'),
            array('magento_version_to', '*')
        );
    }

    /**
     * Edit Theme

     * @test
     */
    public function editThemeInfo()
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => 'Electron', 'theme_title' => $this->generate('string', 65, ':alnum:')));
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        $editData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => '%noValue%',
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
                 'magento_version_from' => $this->themeHelper()->generateVersion(),
                 'magento_version_to' => $this->themeHelper()->generateVersion()
            )
        );
        //Steps
        $this->themeHelper()->createTheme($themeData);
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->themeHelper()->openTheme($searchData);
        $this->fillFieldset($editData['theme_settings'], 'theme_settings');
        $this->fillFieldset($editData['requirements'], 'requirements');
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_theme');
        //Verify
        $this->validatePage('edit_theme');
        $editData['theme_settings']['theme_parent'] = $themeData['theme_settings']['theme_parent'];
        $this->themeHelper()->verifyTheme($editData);
    }

    /**
     * Reset button functionality
     * @test
     */
    public function resetThemeForm()
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => 'Magento Fixed Design', 'theme_title' => $this->generate('string', 65, ':alnum:')));
        $searchData = $this->loadDataSet('Theme', 'theme_search_data',
            array('theme_title' => $themeData['theme_settings']['theme_title']));
        $editData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => '%noValue%',
                 'theme_version' => $this->themeHelper()->generateVersion(),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
                 'magento_version_from' => $this->themeHelper()->generateVersion(),
                 'magento_version_to' => $this->themeHelper()->generateVersion()
            )
        );
        //Steps:
        $this->themeHelper()->createTheme($themeData, false);
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->fillFieldset($editData['theme_settings'], 'theme_settings');
        $this->fillFieldset($editData['requirements'], 'requirements');
        $this->clickButton('reset');
        $this->clickButton('save_theme');
        $this->assertMessagePresent('success', 'success_saved_theme');
        //Verify
        $this->themeHelper()->openTheme($searchData);
        $this->themeHelper()->verifyTheme($themeData);

        return $themeData;
    }

    /**
     * @depends
     * @dataProvider allThemeCssLinks
     * @test
     */
    public function verifyThemeCssLinks($linkName, $linkQty)
    {
        $themeData = $this->loadDataSet('Theme', 'new_theme',
            array('theme_parent' => 'Magento Demo',
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
            array('framework_files', '7'),
            array('library_files', '1'),
            array('theme_files', '3'),
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
            array('theme_parent' => 'Magento Demo',
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
            array('Mage_Catalog--widgets.css', 'mage_catalog_widget'),//0
            array('Mage_Catalog__zoom.css', 'mage_catalog_zoom'), //1
            array('Mage_Cms__widgets.css', 'mage_cms_widgets'), //2
            array('Mage_Oauth--css-oauth-simple.css', 'mage_oauth_css_oauth_simple'), //3
            array('Mage_Page__css_tabs.css', 'mage_page_css_tabs'), //4
            array('Mage_Reports__widgets.css', 'mage_reports_widgets'), //5
            array('Mage_Widget__widgets.css', 'mage_widget_widgets'), //6
            array('mage-calendar.css', 'mage_calendar'), //7
            array('css_print.css', 'css_print'), //8
            array('css_styles-ie.css', 'css_style_ie'), //9
            array('css_styles.css', 'css_style'), //10
            );
    }


    /**
     * TBD. Should be resolved problem with file upload
     *
     * Upload custom.css
     * @depends createOnlyRequiredFilledFields
     * @param array $themeData
     * @test
     */
    public function uploadThemeCss($themeData)
    {
        $this->markTestIncomplete('Incomplete because problem with upload file.');

        //Steps:
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($themeData);
        $this->openTab('css_editor');
        $this->assertFalse($this->controlIsEditable('field','upload_css'));
        $this->assertFalse($this->controlIsEditable('field','download_css'));
        $appConfig = $this->getApplicationConfig();
        if (!array_key_exists('downloadDir', $appConfig)) {
            $this->fail('downloadDir is not set in application config');
        }
        $downloadDir  = $appConfig['downloadDir'];
        $fileName = 'Mage_Catalog__widgets.css';
        $filePath = $downloadDir . DIRECTORY_SEPARATOR . $fileName;
//        $this->fillField('select_css_file_to_upload', $filePath); //Problem with file upload.
//        $this->fillForm(array('select_css_file_to_upload' => $filePath)); try to find workaround
//        $this->assertTrue($this->controlIsEditable('field','upload_css'));
    }

    /**
     * TBD. Should be resolved problem with file upload
     *
     * Upload JS files
     * @depends createOnlyRequiredFilledFields
     * @param array $themeData
     * @test
     */
    public function uploadThemeJs($themeData)
    {
        $this->markTestIncomplete('Incomplete because problem with upload file.');

        //Steps:
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($themeData);
        $this->openTab('js_editor');
        $this->assertFalse($this->controlIsEditable('field','upload_js'));
        $appConfig = $this->getApplicationConfig();
        if (!array_key_exists('downloadDir', $appConfig)) {
            $this->fail('downloadDir is not set in application config');
        }
        $downloadDir  = $appConfig['downloadDir'];
        $fileName = 'Mage_Catalog__widgets.css';
        $filePath = $downloadDir . DIRECTORY_SEPARATOR . $fileName;
//        $this->fillField('select_js_file_to_upload', $filePath); //Problem with file upload.
//        $this->assertTrue($this->controlIsEditable('field','upload_js'));
    }

    /**
     * Delete all virtual themes
     * @test
     */
    public function deleteAllThemes()
    {
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