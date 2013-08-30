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
class Magento_Tax_Model_Resource_Sales_Order_Tax_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Tax_Model_Sales_Order_Tax', 'Magento_Tax_Model_Resource_Sales_Order_Tax');
    }

    /**
     * Retrieve order tax collection by order identifier
     *
     * @param Magento_Object $order
     * @return Magento_Tax_Model_Resource_Sales_Order_Tax_Collection
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
