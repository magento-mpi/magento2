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
 * @method Community2_Mage_CmsWidgets_Helper helper(string $className)
 */
class Enterprise2_Mage_CmsWidgets_Helper extends Enterprise_Mage_CmsWidgets_Helper
{
    /**
     * Fills settings for creating widget
     *
     * @param array $settings
     */
    public function fillWidgetSettings(array $settings)
    {
        $this->helper('Community2/Mage/CmsWidgets/Helper')->fillWidgetSettings($settings);
    }

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
