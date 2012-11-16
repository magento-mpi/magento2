<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsWidgets
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
//    /**
//     * Fills settings for creating widget
//     *
//     * @param array $settings
//     */
//    public function fillWidgetSettings(array $settings)
//    {
//        if ($settings) {
//            $this->addParameter('dropdownXpath', $this->_getControlXpath('dropdown', 'type'));
//            $this->addParameter('optionText', $settings['type']);
//            $type = $this->getControlAttribute('pageelement', 'dropdown_option_text', 'value');
//            $this->addParameter('type', str_replace('/', '-', $type));
//            $packageTheme = array_map('trim', (explode('/', $settings['design_package_theme'])));
//            $this->addParameter('package', $packageTheme[0]);
//            $this->addParameter('theme', $packageTheme[1]);
//            $this->fillFieldset($settings, 'settings_fieldset');
//        }
//        $this->clickButton('continue', false);
//        $this->pleaseWait();
//        $this->validatePage('add_widget_options');
//    }
//
//    /**
//     * Fills settings for creating widget
//     *
//     * @param array $settings
//     */
//    public function fillWidgetSettings(array $settings)
//    {
//        $this->helper('Community2/Mage/CmsWidgets/Helper')->fillWidgetSettings($settings);
//    }

    /**
     * Opens widget
     *
     * @param array $searchWidget
     */
    public function openWidget(array $searchWidget)
    {
        parent::openWidget($searchWidget);
        $this->pleaseWait();
    }

    /**
     * Fills "Widget Options" tab
     *
     * @param array $widgetOptions
     */
    public function fillWidgetOptions(array $widgetOptions)
    {
        if (array_key_exists('banner_name', $widgetOptions)) {
            $this->searchAndChoose(array('filter_banner_name' => $widgetOptions['banner_name']), 'specify_banner_grid');
        }
        parent::fillWidgetOptions($widgetOptions);
    }
}