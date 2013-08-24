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
class Core_Mage_Theme_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create new virtual theme
     *
     * @param array $themeData
     * @param bool $save
     *
     */
    public function createTheme($themeData, $save = true)
    {
        $this->navigate('theme_list');
        $this->elementIsPresent('theme_grid');
        $this->clickButton('add_new_theme');
        $this->fillThemeGeneralTab($themeData);
        if ($save) {
            $this->saveForm('save_theme');
        }
    }

    /**
     * Fill fields on General tab
     *
     * @param array $themeData
     */
    public function fillThemeGeneralTab($themeData)
    {
        if (isset($themeData['theme_settings'])) {
            $this->fillFieldset($themeData['theme_settings'], 'theme_settings');
        } else {
            $this->fail('No information about Theme Settings to fulfill');;
        }
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
        $this->elementIsPresent('theme_grid');
        $xpath = $this->_getControlXpath('pageelement', 'theme_grid_theme_row_by_title');
        $locator = sprintf($xpath, $title);
        $element = $this->getElement($locator);
        $id = $this->defineIdFromUrl($element->attribute('title'));
        return $id;
    }

    /**
     * Search theme
     *
     * @param array $themeData
     *
     * @return string
     */
    public function searchTheme($themeData)
    {
        $searchData = $this->_prepareDataForSearch($themeData);
        $this->navigate('theme_list');
        $themeLocator = $this->search($searchData, 'theme_list_grid');

        return $themeLocator;
    }

    /**
     * Open theme.
     *
     * @param array $themeData
     */
    public function openTheme(array $themeData)
    {
        $themeLocator = $this->searchTheme($themeData);
        $this->assertNotNull($themeLocator, 'Theme is not found');
        $themeRowElement = $this->getElement($themeLocator);
        $themeUrl = $themeRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Theme Title');
        $cellElement = $this->getChildElement($themeRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($themeUrl));
        //Open theme
        $this->url($themeUrl);
        $this->validatePage('edit_theme');
    }

    /**
     * Verify entered information
     *
     * @param array $themeData
     */
    public function verifyTheme(array $themeData)
    {
        if (isset($themeData['theme_settings'])) {
            $this->openTab('general');
            $this->verifyForm($themeData['theme_settings'], 'general');
        } else {
            $this->fail('No information about Theme Settings to verify');
        }
    }

    /**
     * Generate version according to format
     *
     * @return string
     */
    public function generateVersion()
    {
        return '1.' . implode('.',str_split($this->generate('string', 3, ':digit:')));
    }
}
