<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Data Api destination states
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Source_Destinationstates implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve option array with destinations
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Gdata_Gshopping_Extension_Control::DEST_MODE_DEFAULT,  'label' => __('Default')),
            array('value' => Magento_Gdata_Gshopping_Extension_Control::DEST_MODE_REQUIRED, 'label' => __('Required')),
            array('value' => Magento_Gdata_Gshopping_Extension_Control::DEST_MODE_EXCLUDED, 'label' => __('Excluded'))
        );
    }
}
