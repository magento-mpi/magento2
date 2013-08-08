<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source model for Wysiwyg toggling
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Config_Source_Wysiwyg_Enabled implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Cms_Model_Wysiwyg_Config::WYSIWYG_ENABLED,
                'label' => Mage::helper('Mage_Cms_Helper_Data')->__('Enabled by Default')
            ),
            array(
                'value' => Mage_Cms_Model_Wysiwyg_Config::WYSIWYG_HIDDEN,
                'label' => Mage::helper('Mage_Cms_Helper_Data')->__('Disabled by Default')
            ),
            array(
                'value' => Mage_Cms_Model_Wysiwyg_Config::WYSIWYG_DISABLED,
                'label' => Mage::helper('Mage_Cms_Helper_Data')->__('Disabled Completely')
            )
        );
    }
}
