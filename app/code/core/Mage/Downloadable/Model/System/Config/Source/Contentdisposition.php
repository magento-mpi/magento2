<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Content Disposition Source
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_System_Config_Source_Contentdisposition
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'attachment',
                'label' => Mage::helper('Mage_Downloadable_Helper_Data')->__('attachment')
            ),
            array(
                'value' => 'inline',
                'label' => Mage::helper('Mage_Downloadable_Helper_Data')->__('inline')
            )
        );
    }
}

