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
 * Google Content Item statues Source
 *
 * @category   Mage
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Source_Statuses
{
    /**
     * Retrieve option array with Google Content item's statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        return array(
            '0' => Mage::helper('Magento_GoogleShopping_Helper_Data')->__('Yes'),
            '1' => Mage::helper('Magento_GoogleShopping_Helper_Data')->__('No')
        );
    }
}
