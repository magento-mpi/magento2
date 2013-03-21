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
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-4638:</p>
     *
     * @test
     */
    public function openGridPage()
    {
        $this->navigate('theme_list');
        $this->validatePage('theme_list');
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_grid'), 'Theme grid table is not present');
    }

    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-4638:</p>
     *
     * @test
     */
    public function openNewThemePage()
    {
        $this->navigate('theme_list');
        $this->clickButton('add_new_theme');
        $this->validatePage('new_theme');
        $this->assertTrue($this->controlIsPresent('fieldset', 'theme'), 'Theme form fieldset is not present');
    }

    /**
     * Retest Back button functionality
     * @test
     */
    public function backToGrid()
    {
        //Steps
        $this->navigate('theme_list');
        $this->clickButton('add_new_theme');
        $this->clickButton('back');
        //Verify
        $this->validatePage('theme_list');
        $this->assertTrue($this->controlIsPresent('pageelement', 'theme_grid'), 'Theme grid table is not present');
    }

    /**
     * Create Theme with required field only
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6663
     */
    public function createOnlyRequiredFilledFields()
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'default_new_theme');
        //Steps:
        $this->themeHelper()->createTheme($themeData);
        //Verify:
        $this->assertMessagePresent('success', 'success_saved_theme');
        $searchData = $this->_prepareDataForSearch($themeData['theme']);
        $themeLocator = $this->search($searchData, 'theme_list_grid');
        $this->assertNotNull($themeLocator, 'Theme is not found');

        return $themeData;
    }

    /**
     * Create Theme with all changed fields
     *
     * @test
     */
    public function createWithAllFields()
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'all_fields');
        //Steps:
        $this->themeHelper()->createTheme($themeData);
        //Verify:
        $this->assertMessagePresent('success', 'success_saved_theme');
        $searchData = $this->_prepareDataForSearch($themeData['theme']);
        $themeLocator = $this->search($searchData, 'theme_list_grid');
        $this->assertNotNull($themeLocator, 'Theme is not found');

        return $themeData;
    }

    /**
     * Edit Theme
     * @param $themeData
     * @depends createWithAllFields
     * @test
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
        $this->validatePage('edit_theme');
        $this->clickButton('save_theme');
        //Verify:
        $this->assertMessagePresent('success', 'success_saved_theme');
        $searchData = $this->_prepareDataForSearch($editData['theme']);
        $themeLocator = $this->search($searchData, 'theme_list_grid');
        $this->assertNotNull($themeLocator, 'Theme is not found');

        $this->themeHelper()->deleteTheme($editData);
    }

    /**
     * Reset button functionality
     * @param $themeData
     * @depends createWithAllFields
     * @test
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
     * Empty required fields.
     *
     * @param $emptyField
     * @param $fieldType
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @test
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data:
        $themeData = $this->loadDataSet('Theme', 'default_new_theme', array($emptyField => ''));
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
            array('theme_parent_id', 'dropdown'),
            array('theme_version', 'field'),
            array('theme_title', 'field'),
            array('magento_version_from', 'field'),
            array('magento_version_to', 'field'),
        );
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
     * @test
     */
    public function downloadThemeCss($fileName, $linkName, $themeData)
    {
        //Steps:
        $this->navigate('theme_list');
        $this->themeHelper()->openTheme($themeData);
        $this->openTab('css_editor');
        $this->clickControl('link', $linkName, false);
        //Verify:
        $appConfig = $this->getApplicationConfig();
        if (!array_key_exists('downloadDir', $appConfig)) {
            $this->fail('downloadDir is not set in application config');
        }
        $downloadDir  = $appConfig['downloadDir'];
        $filePath = $downloadDir . DIRECTORY_SEPARATOR . $fileName;
        while (!file_exists($filePath)) {
            sleep(1);
        }
        $this->assertTrue(file_exists($filePath), 'File was not downloaded');
        $this->assertTrue(unlink($filePath), 'File was not deleted');
    }

    public function allThemeCss()
    {
        return array(
            array('Mage_Catalog--widgets.css', 'mage_catalog_widget'),
            array('Mage_Catalog--zoom.css', 'mage_catalog_zoom'),
            array('Mage_Cms--widgets.css', 'mage_cms_widgets'),
            array('Mage_Oauth--css_oauth-simple.css', 'mage_oauth_css_oauth_simple'),
            array('Mage_Page--css_tabs.css', 'mage_page_css_tabs'),
            array('Mage_Reports--widgets.css', 'mage_reports_widgets'),
            array('Mage_Widget--widgets.css', 'mage_widget_widgets'),
            array('Social_Facebook--css_facebook.css', 'social_facebook_css_facebook'),
            array('mage_calendar.css', 'mage_calendar'),
            array('css_print.css', 'css_print'),
            array('css_styles-ie.css', 'css_style_ie'),
            array('css_styles.css', 'css_style')
            );
    }

    /**
     * clear test
     * @depends createOnlyRequiredFilledFields
     * @params $themeData
     * @test
     */
    public function deleteTheme($themeData)
    {
        $this->themeHelper()->deleteTheme($themeData);
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
     * @test
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