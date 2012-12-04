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
     * <p>Test Theme selector page when no customized themes</p>
     *
     * @TestlinkId TL-MAGE-6478
     */
    public function testFirstEntranceWithNoCustomizedTheme()
    {
        $this->loginAdminUser();
        $this->themeHelper()->deleteAllCustomizedTheme();

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
    public function testFirstEntranceWithCustomizedTheme()
    {
        $this->loginAdminUser();
        $this->themeHelper()->createTheme();

        $this->navigate('design_editor_selector');
        $this->assertElementNotPresent($this->_getControlXpath('pageelement', 'header_available_themes'));
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'selector_tabs_container'));
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'customized_themes_tab_content'));
        $this->assertVisible($this->_getControlXpath('pageelement', 'customized_themes_tab_content'));
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'available_themes_tab_content'));
        $this->assertNotVisible($this->_getControlXpath('pageelement', 'available_themes_tab_content'));
        $this->assertNotVisible($this->_getControlXpath('pageelement', 'theme_list'));

        $this->clickControl('link', 'available_themes_tab', false);
        $this->assertVisible($this->_getControlXpath('pageelement', 'available_themes_tab_content'));
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'theme_list'));
        $this->assertVisible($this->_getControlXpath('pageelement', 'theme_list'));
        $this->assertNotVisible($this->_getControlXpath('pageelement', 'customized_themes_tab_content'));

        $this->themeHelper()->deleteAllCustomizedTheme();
    }
}
