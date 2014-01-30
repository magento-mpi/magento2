<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wrapper for \Magento\Sales\Model\Order sales model
 */
namespace Magento\Payment\Model\Cart\SalesModel;

class Order implements \Magento\Payment\Model\Cart\SalesModel\SalesModelInterface
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
     * Wrapper for \Magento\Object getDataUsingMethod method
     *
     * @param string $key
     * @param mixed $args
     * @return mixed
     */
    public function getDataUsingMethod($key, $args = null)
    {
        return $this->_salesModel->getDataUsingMethod($key, $args);
    }

    /**
     * Return object that contains tax related fields
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getTaxContainer()
    {
        return $this->_salesModel;
    }
}
