<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order Tax Collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Tax_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Order_Tax', 'Mage_Sales_Model_Resource_Order_Tax');
    }

    /**
     * Load by order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Resource_Order_Tax_Collection
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
