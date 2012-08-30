<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsPages
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
class Enterprise2_Mage_CmsPages_Helper extends Core_Mage_CmsPages_Helper
{
    /**
     * Insert widget
     *
     * @param array $widgetData
     * @param string $buttonName
     */
    public function insertWidget(array $widgetData, $buttonName = 'insert_widget')
    {
        $chooseOption = (isset($widgetData['chosen_option'])) ? $widgetData['chosen_option'] : array();
        if ($this->controlIsPresent('link', 'wysiwyg_insert_widget')) {
            $this->clickControl('link', 'wysiwyg_insert_widget', false);
        } else {
            $this->clickButton($buttonName, false);
        }
        $this->waitForAjax();
        $this->fillForm($widgetData);
        if ($chooseOption) {
            $this->cmsPagesHelper()->selectOptionItem($chooseOption);
        }
        $this->clickButton('submit_widget_insert', false);
        $this->waitForAjax();
    }

    /**
     * Inserts variable
     *
     * @param string $variable
     * @param string $buttonName
     */
    public function insertVariable($variable, $buttonName = 'insert_variable')
    {
        if ($this->controlIsPresent('link', 'wysiwyg_insert_variable')) {
            $this->clickControl('link', 'wysiwyg_insert_variable', false);
        } else {
            $this->clickButton($buttonName, false);
        }
        $this->waitForAjax();
        $this->addParameter('variableName', $variable);
        $this->clickControl('link', 'variable', false);
    }




}