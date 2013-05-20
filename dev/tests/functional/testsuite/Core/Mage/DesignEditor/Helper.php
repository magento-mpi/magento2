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
     * Helper class
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Core_Mage_DesignEditor_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Move mouse over Tile
     *
     * @param $tile
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function mouseOver($tile)
    {
        $tileXpath = $this->_getControlXpath('pageelement', $tile);
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileElement
         */
        $tileElement = $this->getElement($tileXpath);
        $this->moveto($tileElement);
        return $tileElement;
    }

    /**
     * Delete theme
     */
    public function deleteTheme()
    {
        $this->clickButtonAndConfirm('delete_theme_button', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_deleted_theme');
    }

    /**
     * Function for switching on/off Design mode
     * @param $statusData
     */
    public function selectModeSwitcher($statusData)
    {
        $script = "return jQuery('#product-online-switcher').prop('checked')";
        $status = $this->execute(array('script' => $script, 'args' => array()));
        if (($status && $statusData == 'Disabled') || (!$status && $statusData == 'Enabled')) {
            $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'mode_switcher');
        }
    }

    /**
     * Assign theme from Available theme tab.
     */
    public function assignFromAvailableThemeTab($themeTitle = 'Magento Fixed Design')
    {
        $this->clickControl('link', 'available_themes_tab', false);
        $this->waitForAjax();
        $this->addParameter('themeTitle', $themeTitle);
        $this->mouseOver('thumbnail');
        $this->focusOnThemeElement('button', 'assign_theme_button');
        $this->clickButtonAndConfirm('assign_theme_button', 'confirmation_for_assign_to_default', false);
        $this->_windowId = $this->selectLastWindow();
        $themeId = $this->defineParameterFromUrl('theme_id', $url = null);
        $this->addParameter('id', $themeId);
        $this->validatePage('assigned_theme_default_in_design');

        $this->closeWindow($this->_windowId);
        $this->selectLastWindow();
        $this->validatePage();


        return $themeId;
    }

    /**
     * Focus on the theme
     */
    public function focusOnThemeElement($controlType, $controlName)
    {
        $locator = $this->_getControlXpath($controlType, $controlName);
        $availableElement = $this->elementIsPresent($locator);
        $this->focusOnElement($availableElement);
        $this->pleaseWait();
    }

}