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
 * Rule report resource model with aggregation by updated at
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesRule\Model\Resource\Report\Rule;

class Updatedat extends \Magento\SalesRule\Model\Resource\Report\Rule\Createdat
{
    /**
     * Resource Report Rule constructor
     *
     */
    protected function _construct()
    {
        $this->_init('coupon_aggregated_updated', 'id');
    }

    /**
     * Aggregate Coupons data by order updated at
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\SalesRule\Model\Resource\Report\Rule\Updatedat
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByOrder('updated_at', $from, $to);
    }
}
