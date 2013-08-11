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
 * Google Content Item statues Source
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Source_Statuses
{
    /**
     * Retrieve option array with Google Content item's statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        return array(
            '0' => __('Yes'),
            '1' => __('No')
        );
    }
}
