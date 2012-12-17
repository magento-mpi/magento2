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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Theme_Helper extends Mage_Selenium_TestCase
{
    /**
     * <p>Delete all virtual themes</p>
     */
    public function deleteAllVirtualThemes()
    {
        $this->navigate('theme_list');
        $this->isElementPresent('theme_grid');

        $xpath = $this->_getControlXpath('pageelement', 'theme_grid_theme_path_empty_column');
        while ($this->isElementPresent($xpath)) {
            $this->clickControl('pageelement', 'theme_grid_theme_path_empty_column');
            $this->chooseOkOnNextConfirmation();
            $this->clickButton('delete_theme');
            $this->assertConfirmation('Are you sure you want to do this?');
            $this->assertMessagePresent('success', 'success_deleted_theme');
        }
    }

    /**
     * <p>Create new virtual theme</p>
     *
     * $themeData can be:
     * - string dataSet in Theme
     * - array('theme', 'requirements')
     * - array('theme')
     * - array()
     * @param string|array $themeData
     */
    public function createTheme($themeData = 'default_new_theme')
    {
        $this->navigate('theme_list');
        $this->isElementPresent('theme_grid');

        $this->clickButton('add_new_theme');

        if (is_string($themeData)) {
            $themeData = $this->loadDataSet('Theme', $themeData);
        }

        if (isset($themeData['theme'])) {
            $this->fillFieldset($themeData['theme'], 'theme');
        } else {
            $this->fillFieldset($themeData, 'theme');
        }

        if (isset($themeData['requirements'])) {
            $this->fillFieldset($themeData['requirements'], 'requirements');
        }

        $this->clickButton('save_theme');
        $this->assertMessagePresent('success', 'success_saved_theme');
    }

    /**
     * Define parameter theme_id by theme title
     *
     * @param string $title
     * @return string
     */
    public function getThemeIdByTitle($title)
    {
        $this->navigate('theme_list');
        $this->isElementPresent('theme_grid');
        $xpath = $this->_getControlXpath('pageelement', 'theme_grid_theme_row_by_title');
        $xpath = sprintf($xpath, $title);
        $urlFromTitleAttribute = $this->getAttribute($xpath . '@title');
        $id = $this->defineIdFromUrl($urlFromTitleAttribute);
        return $id;
    }
}
