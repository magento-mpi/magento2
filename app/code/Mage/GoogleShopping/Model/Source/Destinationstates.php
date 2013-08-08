<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Data Api destination states
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Source_Destinationstates
{
    /**
     * Retrieve option array with destinations
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Varien_Gdata_Gshopping_Extension_Control::DEST_MODE_DEFAULT,  'label' => __('Default')),
            array('value' => Varien_Gdata_Gshopping_Extension_Control::DEST_MODE_REQUIRED, 'label' => __('Required')),
            array('value' => Varien_Gdata_Gshopping_Extension_Control::DEST_MODE_EXCLUDED, 'label' => __('Excluded'))
        );
    }
}
