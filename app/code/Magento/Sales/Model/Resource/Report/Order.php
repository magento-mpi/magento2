<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order entity resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Report;

class Order extends \Magento\Sales\Model\Resource\Report\AbstractReport
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Sales\Model\Resource\Report\Order
     */
    public function aggregate($from = null, $to = null)
    {
        \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Order\Createdat')->aggregate($from, $to);
        \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Order\Updatedat')->aggregate($from, $to);
        $this->_setFlagData(\Magento\Reports\Model\Flag::REPORT_ORDER_FLAG_CODE);

        return $this;
    }
}
