<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales report coupons collection
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Resource_Report_Updatedat_Collection
    extends Magento_SalesRule_Model_Resource_Report_Collection
{
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'coupon_aggregated_updated';
}
