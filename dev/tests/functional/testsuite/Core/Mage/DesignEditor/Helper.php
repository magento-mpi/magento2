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
     * @return string
     */
    public function assignFromAvailableThemeTab()
    {
        $this->openTab('available_themes');
        $this->waitForAjax();
        $themeId = $this->getControlElement('pageelement', 'first_theme_thumbnail')->attribute('id');
        $this->addParameter('themeId', $themeId);
        $this->focusOnThemeElement('button', 'assign_theme_button');
        $this->mouseOver('thumbnail');
        $this->clickButton('assign_theme_button', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'assign_theme_confirmation');
        $this->assertTrue($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'confirmation_for_assign_to_default'));
        $this->clickButton('assign', false);
        sleep(2);
        $this->_windowId = $this->selectLastWindow();
        $themeId = $this->defineParameterFromUrl('theme_id', $url = null);
        $this->addParameter('id', $themeId);
        $this->validatePage('assigned_theme_default_in_navigation');

        $this->closeWindow($this->_windowId);
        $this->selectLastWindow('');
        $this->validatePage();
        $this->assertMessagePresent('success', 'assign_success');
        $this->clickButton('close');

        return $themeId;
    }

    /**
     * Focus on the theme
     * @param string $controlType
     * @param string $controlName
     */
    public function focusOnThemeElement($controlType, $controlName)
    {
        $availableElement = $this->getControlElement($controlType, $controlName);
        $this->focusOnElement($availableElement);
        $this->pleaseWait();
    }

    /**
     * Drag and drop VDE block in design mode
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $block
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $destinationContainer
     * @return Core_Mage_Vde_Helper
     */
    public function dragBlock($block, $destinationContainer)
    {
        $this->assertNotNull($block, 'Unable to drag block: element not exists');
        $block->click();
        $this->moveto($block);
        $this->buttondown();
        $this->moveto($destinationContainer);
        $this->buttonup();
        $this->waitForPageToLoad();
    }

    /**
     * Get block element by data-name or class name
     *
     * @param string $name data-name attribute in design mode or class name in navigation mode
     * @param bool $designMode
     * @return null|PHPUnit_Extensions_Selenium2TestCase_Element
     */

    public function getBlock($name, $designMode = false)
    {
        if ($designMode) {
            $mode = 'design';
            $this->addParameter('dataName', $name);
        } else {
            $mode = 'navigation';
            $this->addParameter('className', $name);
        }
        $this->setVdePage($mode);
        if ($this->controlIsPresent('fieldset', 'iframe')) {
            $this->frame('vde_container_frame');
        }
        if ($this->controlIsPresent('pageelement', 'vde_element')) {
            return $this->getControlElement('pageelement', 'vde_element');
        }

        return null;
    }

    /**
     * Get wrapper element
     *
     * @param PHPUnit_Extensions_Selenium2TestCase_Element $block
     * @param bool $designMode
     * @return null|PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function getContainer($block, $designMode = false)
    {
        if ($designMode) {
            $mode = 'design';
            $this->addParameter('dataName', $block->attribute('data-name'));
        } else {
            $mode = 'navigation';
            $this->addParameter('className', $block->attribute('class'));
        }
        $this->setVdePage($mode);
        if ($this->controlIsPresent('fieldset', 'iframe')) {
            $this->frame('vde_container_frame');
        }
        $xpath = $this->_getControlXpath('pageelement', 'vde_element') . "/parent::div/parent::div";
        if ($this->elementIsPresent($xpath)) {
            return $this->getElement($xpath);
        }

        return null;
    }

    /**
     * Set area and current page in design or layout mode
     *
     * @param string $mode
     * @param string $frontPage
     */
    public function setVdePage($mode, $frontPage = '')
    {
        if ($mode == 'navigation' && $frontPage != '') {
            // Set provided frontend page as current in navigation mode
            $this->setArea('frontend');
            $this->setCurrentPage($frontPage);
        } else {
            $this->getArea() == 'admin' ? : $this->setArea('admin');
            $this->getCurrentPage() == 'vde_' . $mode ? : $this->setCurrentPage('vde_' . $mode);
        }
    }

    /**
     * Select store view for assign/edit
     * @return string
     */
    public function chooseStoreView()
    {
        $xpathStoreView = $this->_getControlXpath('pageelement', 'store_view_label_by_title');
        $storeViewId = $this->getElement($xpathStoreView)->attribute('for');
        $this->addParameter('storeId', $storeViewId);
        $xpathStoreViewInput = $this->_getControlXpath('pageelement', 'store_view_input_by_id');
        $xpathStoreViewInput = sprintf($xpathStoreViewInput, $storeViewId);
        $storeViewName = $this->getElement($xpathStoreViewInput)->attribute('name');
        $this->fillCheckbox($storeViewName, 'Yes', $xpathStoreViewInput);

        return $storeViewId = str_replace('storeview_', '', $storeViewId);
    }

}