<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source model for Wysiwyg toggling
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Config_Source_Wysiwyg_Enabled implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Cms_Model_Wysiwyg_Config::WYSIWYG_ENABLED,
                'label' => __('Enabled by Default')
            ),
            array(
                'value' => Magento_Cms_Model_Wysiwyg_Config::WYSIWYG_HIDDEN,
                'label' => __('Disabled by Default')
            ),
            array(
                'value' => Magento_Cms_Model_Wysiwyg_Config::WYSIWYG_DISABLED,
                'label' => __('Disabled Completely')
            )
        );
    }
}
