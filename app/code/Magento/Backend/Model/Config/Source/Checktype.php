<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Send to a Friend Limit sending by Source
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Source_Checktype implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve Check Type Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Sendfriend_Helper_Data::CHECK_IP,
                'label' => __('IP Address')
            ),
            array(
                'value' => Magento_Sendfriend_Helper_Data::CHECK_COOKIE,
                'label' => __('Cookie (unsafe)')
            ),
        );
    }
}
