<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adapter for \Magento\Sales\Model\Quote sales model
 */
namespace Magento\Payment\Model\Cart\SalesModel;

class Quote implements \Magento\Payment\Model\Cart\SalesModel\SalesModelInterface
{
    /**
     * Sales quote model instance
     *
     * @var \Magento\Sales\Model\Quote
     */
    protected $_salesModel;

    /**
     * @var \Magento\Sales\Model\Quote\Address
     */
    protected $_address;

    /**
     * @param \Magento\Sales\Model\Quote $salesModel
     */
    public function __construct(\Magento\Sales\Model\Quote $salesModel)
    {
        $this->_salesModel = $salesModel;
        $this->_address = $this->_salesModel->getIsVirtual() ?
            $this->_salesModel->getBillingAddress() : $this->_salesModel->getShippingAddress();
    }

    /**
     * Get model which is wrapped with adapter
     *
     * @return \Magento\Sales\Model\Quote
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
                'qty'           => (int)$item->getTotalQty(),
                'price'         => $item->isNominal() ? 0 : (float)$item->getBaseCalculationPrice(),
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
        return $this->_address->getBaseTaxAmount();
    }

    /**
     * @return float|null
     */
    public function getBaseShippingAmount()
    {
        return $this->_address->getBaseShippingAmount();
    }

    /**
     * @return float|null
     */
    public function getBaseDiscountAmount()
    {
        return $this->_address->getBaseDiscountAmount();
    }
}
