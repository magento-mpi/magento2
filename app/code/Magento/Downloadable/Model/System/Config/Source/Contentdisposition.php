<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Content Disposition Source
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_System_Config_Source_Contentdisposition
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'attachment',
                'label' => Mage::helper('Magento_Downloadable_Helper_Data')->__('attachment')
            ),
            array(
                'value' => 'inline',
                'label' => Mage::helper('Magento_Downloadable_Helper_Data')->__('inline')
            )
        );
    }
}

