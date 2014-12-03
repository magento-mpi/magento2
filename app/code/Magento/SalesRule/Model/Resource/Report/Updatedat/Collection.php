<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Resource\Report\Updatedat;

/**
 * Sales report coupons collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\SalesRule\Model\Resource\Report\Collection
{
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'salesrule_coupon_aggregated_updated';
}
