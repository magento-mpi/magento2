<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Resource\Report\Rule;

/**
 * Rule report resource model with aggregation by updated at
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Updatedat extends \Magento\SalesRule\Model\Resource\Report\Rule\Createdat
{
    /**
     * Resource Report Rule constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('coupon_aggregated_updated', 'id');
    }

    /**
     * Aggregate Coupons data by order updated at
     *
     * @param mixed|null $from
     * @param mixed|null $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByOrder('updated_at', $from, $to);
    }
}
