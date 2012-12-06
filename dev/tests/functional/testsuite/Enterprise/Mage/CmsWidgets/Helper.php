<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
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
class Enterprise_Mage_CmsWidgets_Helper extends Core_Mage_CmsWidgets_Helper
{
    /**
     * Fills settings for creating widget
     *
     * @param array $settings
     */
    public function fillWidgetSettings(array $settings)
    {
        if ($settings) {
            $xpath = $this->_getControlXpath('dropdown', 'type');
            $type = $this->getValue($xpath . '/option[text()="' . $settings['type'] . '"]');
            $this->addParameter('type', str_replace('/', '-', $type));
            $packageTheme = array_map('trim', (explode('/', $settings['design_package_theme'])));
            $this->addParameter('package', $packageTheme[0]);
            $this->addParameter('theme', $packageTheme[1]);
            $this->fillFieldset($settings, 'settings_fieldset');
        }
        $this->clickButton('continue', false);
        $this->pleaseWait();
        $this->validatePage('add_widget_options');
    }
}