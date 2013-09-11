<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order Tax Collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Resource\Sales\Order\Tax;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('\Magento\Tax\Model\Sales\Order\Tax', '\Magento\Tax\Model\Resource\Sales\Order\Tax');
    }

    /**
     * Retrieve order tax collection by order identifier
     *
     * @param \Magento\Object $order
     * @return \Magento\Tax\Model\Resource\Sales\Order\Tax\Collection
     */
    public function loadByOrder($order)
    {
        $orderId = $order->getId();
        $this->getSelect()
            ->where('main_table.order_id = ?', (int)$orderId)
            ->order('process');
        return $this->load();
    }
}
