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
 * Send to a Friend Limit sending by Source
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Checktype implements Magento_Core_Model_Option_ArrayInterface
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
                'value' => Mage_Sendfriend_Helper_Data::CHECK_IP,
                'label' => Mage::helper('Mage_Backend_Helper_Data')->__('IP Address')
            ),
            array(
                'value' => Mage_Sendfriend_Helper_Data::CHECK_COOKIE,
                'label' => Mage::helper('Mage_Backend_Helper_Data')->__('Cookie (unsafe)')
            ),
        );
    }
}
