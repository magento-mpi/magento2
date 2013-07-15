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
                 'theme_version' => $this->generate('string', 1, ':digit:') . '.'
                                    . $this->generate('string', 1, ':digit:') . '.'
                                    . $this->generate('string', 1, ':digit:') . '.'
                                    . $this->generate('string', 1, ':digit:'),
                 'theme_title' => $this->generate('string', 65, ':alnum:'),
                 'magento_version_from' => $this->generate('string', 1, ':digit:') . '.'
                                           . $this->generate('string', 1, ':digit:') . '.'
                                           . $this->generate('string', 1, ':digit:') . '.'
                                           . $this->generate('string', 1, ':digit:'),
                 'magento_version_to' => $this->generate('string', 1, ':digit:') . '.'
                                         . $this->generate('string', 1, ':digit:') . '.'
                                         . $this->generate('string', 1, ':digit:') . '.'
                                         . $this->generate('string', 1, ':digit:'),
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
     * Delete all virtual themes
     * @test
     */
    public function deleteAllVirtualThemes()
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

    /**
     * Edit Theme
     * @param $themeData
     * @depends createWithAllFields
     * @ test
     */
    public function editTheme($themeData)
    {
        //Data:
        $editData = $this->loadDataSet('Theme', 'edit_theme');
        //Steps:
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($themeData);
        $this->fillFieldset($editData['theme'], 'theme');
        $this->fillFieldset($themeData['requirements'], 'requirements');
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_theme');
        $this->validatePage('edit_theme');
        $this->clickButton('save_theme');
        //Verify:
        $this->assertMessagePresent('success', 'success_saved_theme');
        $searchData = $this->_prepareDataForSearch($editData['theme']);
        $themeLocator = $this->search($searchData, 'theme_list_grid');
        $this->assertNotNull($themeLocator, 'Theme is not found');

    }

    /**
     * Reset button functionality
     * @param $themeData
     * @depends createOnlyRequiredFilledFields
     * @ test
     */
    public function resetThemeForm($themeData)
    {
        //Data:
        $editData = $this->loadDataSet('Theme', 'edit_theme');
        //Steps:
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($themeData);
        $this->fillFieldset($editData['theme'], 'theme');
        $this->fillFieldset($themeData['requirements'], 'requirements');
        $this->clickButton('reset');
        $this->clickButton('save_theme');
        $this->assertMessagePresent('success', 'success_saved_theme');
        //Verify
        $searchData = $this->_prepareDataForSearch($themeData['theme']);
        $themeLocator = $this->search($searchData, 'theme_list_grid');
        $this->assertNotNull($themeLocator, 'Theme is not found');
    }


    /**
     * <p>Notice: setup *.css in mimeType</p>
     * https://wiki.corp.x.com/display/QAA/Configure+FireFox+for+auto+confirm+any+file+types
     *
     * Download theme css files
     *
     * @depends createOnlyRequiredFilledFields
     * @param array $themeData
     * @param $linkName
     * @param $fileName
     * @dataProvider allThemeCss
     * @ test
     */
    public function downloadThemeCss($fileName, $linkName, $themeData)
    {
        //Data
        $fileUrl = $this->getConfigHelper()->getPathToTestFiles($fileName);
        $expectedContent = file_get_contents($fileUrl);
        $expectedContent = str_replace(array("\r\n", "\n"), '', $expectedContent);
        //Steps:
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($themeData);
        $this->openTab('css_editor');

        $selectedFileUrl = $this->getControlAttribute('link', $linkName, 'href');
        $downloadedFileContent = $this->getFile($selectedFileUrl);
        $downloadedFileContent = str_replace(array("\r\n", "\n"), '', $downloadedFileContent);
        $this->assertEquals($expectedContent, $downloadedFileContent, 'File was not downloaded or not equal to expected.');
    }

    public function allThemeCss()
    {
        return array(
            array('Mage_Catalog--widgets.css', 'mage_catalog_widget'),
            array('Mage_Oauth--css_oauth-simple.css', 'mage_oauth_css_oauth_simple'),
            array('Social_Facebook--css_facebook.css', 'social_facebook_css_facebook'),
            array('mage_calendar.css', 'mage_calendar'),
            array('css_print.css', 'css_print'),
            array('css_styles-ie.css', 'css_style_ie'),
            array('css_styles.css', 'css_style'),
            array('js_jqzoom_css_jquery.jqzoom.css', 'js_jqzoom_css_jquery'),
            array('Mage_Cms--widgets.css', 'mage_cms_widgets'),
            array('Mage_Page--css_tabs.css', 'mage_page_css_tabs'),
            array('Mage_Reports--widgets.css', 'mage_reports_widgets'),
            array('Mage_Widget--widgets.css', 'mage_widget_widgets')
            );
    }


    /**
     * TBD. Should be resolved problem with file upload
     *
     * Upload custom.css
     * @depends createOnlyRequiredFilledFields
     * @param array $themeData
     * @ test
     */
    public function uploadThemeCss($themeData)
    {
        $this->markTestIncomplete('WIncomplete because problem with upload file.');

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
     * @ test
     */
    public function uploadThemeJs($themeData)
    {
        $this->markTestIncomplete('WIncomplete because problem with upload file.');

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
}