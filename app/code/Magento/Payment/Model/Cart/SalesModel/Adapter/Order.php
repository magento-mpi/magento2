<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adapter for \Magento\Sales\Model\Order sales model
 */
namespace Magento\Payment\Model\Cart\SalesModel\Adapter;

class Order implements \Magento\Payment\Model\Cart\SalesModel\Adapter\AdapterInterface
{
    /**
     * Sales order model instance
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_salesModel;

    /**
     * @param \Magento\Sales\Model\Order $salesModel
     */
    public function __construct(\Magento\Sales\Model\Order $salesModel)
    {
        $this->_salesModel = $salesModel;
    }

    /**
     * Get model which is wrapped with adapter
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOriginalModel()
    {
        return $this->_salesModel;
    }

    /**
     * Get all items from shopping sales model
     *
     * @return array
     */
    public function getAllItems()
    {
        $resultItems = array();

        foreach ($this->_salesModel->getAllItems() as $item) {
            $resultItems[] = new \Magento\Object(array(
                'parent_item'   => $item->getParentItem(),
                'name'          => $item->getName(),
                'qty'           => (int)$item->getQtyOrdered(),
                'price'         => (float)$item->getBasePrice(),
                'original_item' => $item
            ));
        }

        return $resultItems;
    }

    /**
     * @return float|null
     */
    public function getBaseSubtotal()
    {
        return $this->_salesModel->getBaseSubtotal();
    }

    /**
     * @return float|null
     */
    public function getBaseTaxAmount()
    {
        return $this->_salesModel->getBaseTaxAmount();
    }

    /**
     * @return float|null
     */
    public function getBaseShippingAmount()
    {
        return $this->_salesModel->getBaseShippingAmount();
    }

    /**
     * @return float|null
     */
    public function getBaseDiscountAmount()
    {
        return $this->_salesModel->getBaseDiscountAmount();
    }

    /**
     * @return float|null
     */
    public function getBaseCustomerBalanceAmount()
    {
        return $this->_salesModel->getDataUsingMethod('base_customer_balance_amount');
    }
}
