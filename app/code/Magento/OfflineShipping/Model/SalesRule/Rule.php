<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping Cart Rule data model
 *
 * @category    Magento
 * @package     Magento_OfflineShipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\OfflineShipping\Model\SalesRule;

class Rule
{
    /**
     * Free Shipping option "For matching items only"
     */
    const FREE_SHIPPING_ITEM    = 1;

    /**
     * Free Shipping option "For shipment with matching items"
     */
    const FREE_SHIPPING_ADDRESS = 2;
}
