<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StoreLauncher
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
class Core_Mage_StoreLauncher_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Open Drawer popup
     *
     * @param $tile fieldset name from UIMap
     * @return bool
     */
    public function openDrawer($tile)
    {
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileButton
         */
        $tileButton = null;
        $tileButtons = array('open_drawer', 'edit_drawer');
        foreach($tileButtons as $key => $btnName) {
            if ($this->controlIsPresent('button', $btnName)) {
                $tileElement = $this->mouseOverDrawer($tile);
                $tileButton =
                    $this->getChildElement($tileElement, $this->_getControlXpath('button', $btnName), false);
                if ($tileButton->displayed()) {
                    $tileButton->click();
                    $this->waitForAjax();
                    $this->pleaseWait();
                    return (bool)$this->waitForElement($this->_getControlXpath('pageelement', 'drawer_footer'));
                }
            }
        }
        return false;
    }

    /**
     * Close Drawer popup
     *
     * @return bool
     */
    public function closeDrawer()
    {
        $this->clickButton('close_drawer', false);
        return $this->waitForElementInvisible($this->_getControlXpath('fieldset', 'common_drawer'));
    }

    /**
     * Save Drawer
     */
    public function saveDrawer()
    {
        $this->clickButton('save_my_settings', false);
        $this->waitForAjax();
        return $this->waitForElementInvisible($this->_getControlXpath('fieldset', 'common_drawer'));
    }

    /**
     * Move mouse over Tile
     *
     * @param $tile
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function mouseOverDrawer($tile)
    {
        $tileXpath = $this->_getControlXpath('fieldset', $tile);
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileElement
         */
        $tileElement = $this->getElement($tileXpath);
        $this->moveto($tileElement);
        return $tileElement;
    }

    /**
     * Get Tile element background color
     *
     * @param $element
     * @return null|string
     */
    public function getTileBgColor($element)
    {
        $elementId = $element->attribute('id');
        if ($elementId) {
            $script =
                "return window.getComputedStyle(document.getElementById(arguments[0])).backgroundColor;";
            $elementStyle = $this->execute(array('script' => $script, 'args' => array(0 => $elementId)));
            return $elementStyle;
        }
        return null;
    }
}