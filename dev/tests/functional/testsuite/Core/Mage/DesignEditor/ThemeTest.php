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

        /** 6 - default page size for theme collection */
        $this->assertEquals(6, $defaultElementsCount);
        $this->getEval('window.scrollTo(0,Math.max(document.documentElement.scrollHeight, document.body.scrollHeight,'
            . 'document.documentElement.clientHeight));');
        $this->waitForAjax();

        /** 8 - the number of items on 2 pages */
        $this->assertEquals(8, $this->getXpathCount($xpath));
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
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'preview_default_link'),
            'Preview button is not exists');
        $this->click($this->_getControlXpath('pageelement', 'preview_default_link'));
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
        $this->assertElementPresent($this->_getControlXpath('pageelement', 'preview_demo_link'),
            'Preview button is not exists');
        $this->click($this->_getControlXpath('pageelement', 'preview_demo_link'));
        $this->waitForPageToLoad();
        $this->assertElementPresent('//iframe[@id=\'preview-theme\']', 'iFrame not present in preview page');
    }
}
