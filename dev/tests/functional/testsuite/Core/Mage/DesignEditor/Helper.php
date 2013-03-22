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
     * @param $themeData
     */
    public function deleteTheme($themeData)
    {
        $themeId = $this->themeHelper()->getThemeIdByTitle($themeData['theme']['theme_title']);
        $this->addParameter('id', $themeId);
        $this->navigate('design_editor_selector');
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

}