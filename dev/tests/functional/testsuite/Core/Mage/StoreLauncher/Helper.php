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
        $tile = $this->_getControlXpath('fieldset', $tile);
        $openButton = $this->_getControlXpath('button', 'open_drawer');
        $tileButtons = $this->getElements($tile . $openButton);
        foreach ($tileButtons as $tileButton) {
            /**
             * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileButton
             */
            if ($tileButton->displayed()) {
                $tileButton->click();
                break;
            }
        }
        $this->waitForAjax();
        $this->pleaseWait();
        return !$this->waitForElementInvisible($this->_getControlXpath('fieldset', 'store_launcher'));
    }

    /**
     * Close Drawer popup
     *
     * @return bool
     */
    public function closeDrawer()
    {
        $btnXpath = $this->_getControlXpath('button', 'close_drawer');
        $this->moveto($this->getElement($btnXpath));
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
}