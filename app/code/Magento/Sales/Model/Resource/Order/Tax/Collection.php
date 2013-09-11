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
 * Order Tax Collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Tax;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Order\Tax', '\Magento\Sales\Model\Resource\Order\Tax');
    }

    /**
     * Load by order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Resource\Order\Tax\Collection
     */
    public function loadByOrder($order)
    {
        $orderId = $order->getId();
        $this->getSelect()
            ->where('main_table.order_id = ?', $orderId)
            ->order('process');
        return $this->load();
    }
}
