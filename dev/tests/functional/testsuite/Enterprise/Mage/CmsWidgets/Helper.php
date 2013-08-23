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
    /**
     * Fills "Widget Options" tab
     *
     * @param array $widgetOptions
     */
    public function fillWidgetOptions(array $widgetOptions)
    {
        $this->openTab('widgets_options');
        if (array_key_exists('banner_name', $widgetOptions)) {
            $this->searchAndChoose(array('filter_banner_name' => $widgetOptions['banner_name']), 'specify_banner_grid');
            unset($widgetOptions['banner_name']);
        }
        parent::fillWidgetOptions($widgetOptions);
    }
}
