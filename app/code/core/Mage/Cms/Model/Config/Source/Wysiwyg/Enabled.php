<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source model for Wysiwyg toggling
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Cms_Wysiwyg_Enabled
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
