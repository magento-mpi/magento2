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
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Source;

class Statuses
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
