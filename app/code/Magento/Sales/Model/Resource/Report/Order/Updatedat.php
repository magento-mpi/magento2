<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Report\Order;

/**
 * Order entity resource model with aggregation by updated at
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Updatedat extends Createdat
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_aggregated_updated', 'id');
    }

    /**
     * Aggregate Orders data by order updated at
     *
     * @param string|int|\Zend_Date|array|null $from
     * @param string|int|\Zend_Date|array|null $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByField('updated_at', $from, $to);
    }
}
